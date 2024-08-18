<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceProccessResource\Pages;
use App\Filament\Resources\ServiceProccessResource\Pages\ServiceDetails;
use App\Filament\Resources\ServiceProccessResource\RelationManagers;
use App\Http\Controllers\TaskManagerController;
use App\Http\Controllers\UtilsController;
use App\Jobs\ComandExecJob;
use App\Models\Enviromet;
use App\Models\ServiceProccess;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ServiceProccessResource extends Resource
{
    protected static ?string $model = ServiceProccess::class;

    protected static ?string $label = "Command service";


    protected static ?string $navigationIcon = 'heroicon-c-command-line';

    public static function form(Form $form): Form
    {
        $uuid = (string)Str::uuid();

        return $form
            ->schema([
                TextInput::make('command')->columnSpanFull()->prefixIcon('heroicon-c-command-line'),
                Select::make('env')->options(function () {
                    if (auth()->user()->access !== 2) {
                        return Enviromet::all()->pluck('name', 'id');
                    } else {
                        return Enviromet::where('user', auth()->user()->id)->get()->pluck('name', 'id');
                    }
                })->prefixIcon('heroicon-c-cog'),
                TextInput::make('interval')->numeric()->default(60)->prefixIcon('heroicon-s-clock'),
                Select::make('loggable')->options(['no', 'yes'])->default(1)->prefixIcon('heroicon-m-bars-3-center-left'),
                TextInput::make('tag')->afterStateUpdated(function ($state) {
                    return Str::slug($state);
                })->prefixIcon('heroicon-s-tag')->columnSpanFull()->default(Str::random(5)),
                Hidden::make('uuid')->default($uuid),
                Hidden::make('last_execution')->default(now()),
                Hidden::make('pid')->default(0)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->columns([
                ToggleColumn::make('status')
                    ->afterStateUpdated(function ($record) {
                        if ($record->status == 0) {
                            if ($record->pid > 0) {
                                $exec = (new TaskManagerController($record->command))->kill($record->pid);
                            } else {
                                $exec = (new TaskManagerController($record->command))->kill();
                            }

                            // dd($exec);

                            $record->update(['pid' => 0]);
                            Notification::make()
                                ->info()
                                ->body($exec->message ?? "")
                                ->send();
                        } else {
                            Notification::make()
                                ->info()
                                ->body('started process')
                                ->send();
                        }
                    }),
                TextColumn::make('pid')
                    ->label('PID')
                    ->icon('heroicon-c-list-bullet'),
                TextColumn::make('info')
                    ->label('Info.')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        $status = (new TaskManagerController($record->command))->status();
                        if ($status) {
                            if ($record->status == 0) {
                                return "KILLING";
                            } else {
                                return "RUNNING";
                            }
                        } else if ($record->status == 1) {
                            return "PEDDING";
                        }

                        return "STOPED";
                    })
                    ->color(function ($record) {
                        $status = (new TaskManagerController($record->command))->status();
                        if ($status) {
                            if ($record->status == 0) {
                                return "gray";
                            } else {
                                return "success";
                            }
                        } else if ($record->status == 1) {
                            return "warning";
                        }

                        return "danger";
                    })
                    ->badge(),
                TextColumn::make('command')
                    ->icon('heroicon-c-command-line')
                    ->limit(30),
                TextColumn::make('tag')
                    ->badge('primary')
                    ->icon('heroicon-s-tag'),
                TextColumn::make('uuid')
                    ->limit(20)
                    ->icon('heroicon-c-link')
                    ->url(function ($record) {
                        return "/manager/service-proccesses/{$record->id}/details";
                    }),
                TextColumn::make('interval')
                    ->icon('heroicon-s-clock')
                    ->suffix(' Sec.'),
                TextColumn::make('nex_execution')
                    ->label('Next execution')
                    ->getStateUsing(function ($record) {
                        $timmer = (strtotime($record->last_execution) + $record->interval) - time();
                        return ($timmer > 0 ? $timmer : 0);
                    })
                    ->icon('heroicon-s-clock')
                    ->color(function ($record) {
                        $timmer = (strtotime($record->last_execution) + $record->interval) - time();
                        if ($record->status == 0) {
                            return 'gray';
                        } else if ($timmer < 0) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->suffix(' Sec.'),
                TextColumn::make('last_execution')
                    ->dateTime('d/m/Y H:i')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('force-stop')
                    ->label(function ($record) {
                        $status = (new TaskManagerController($record->command))->status();
                        if ($status) {
                            return 'Kill process';
                        } else {
                            return 'Start Proccess';
                        }
                    })
                    ->color(function ($record) {
                        $status = (new TaskManagerController($record->command))->status();
                        if ($status) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->icon(function ($record) {
                        $status = (new TaskManagerController($record->command))->status();
                        if ($status) {
                            return 'heroicon-s-stop-circle';
                        } else {
                            return 'heroicon-s-play-circle';
                        }
                    })
                    ->requiresConfirmation()
                    ->form([
                        Toggle::make('background')
                            ->label('Execute process in background')
                    ])
                    ->action(function ($record, $data) {
                        if ($data['background']) {
                            if ($record->status == 0) {
                                ComandExecJob::dispatch($record)->onQueue($record->enviroment()->first()->queue()->first()->name);
                                Notification::make()
                                    ->warning()
                                    ->body("Process running in background")
                                    ->send();
                            } else {
                                // ComandExecJob::dispatch($record)->onQueue($record->enviroment()->first()->queue()->first()->name);
                                (new UtilsController())->ExecuteCommand("pkill if '{$record->command}' &");
                                Notification::make()
                                    ->warning()
                                    ->body("Process running in background")
                                    ->send(); 
                            }
                        } else {
                            if ($record->status == 0) {
                                $prepare = "cd {$record->enviroment()->first()->path} && {$record->command}";
                                $pid = (new TaskManagerController())->ExecPid($prepare);
                                $record->update(['status' => 1, 'pid' => $pid->pid, 'last_execution' => now()]);
                                Notification::make()
                                    ->success()
                                    ->body("Command started with success")
                                    ->send();
                            } else {
                                $prepare = "cd {$record->enviroment()->first()->path} && {$record->command}";
                                $pid = (new TaskManagerController())->ExecPid("pkill -f '{$prepare}'");
                                $record->update(['status' => 0, 'pid' => 0, 'last_execution' => now()]);
                                Notification::make()
                                    ->success()
                                    ->body("Command killed with success")
                                    ->send();
                            }
                        }
                    }),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->before(function ($record) {
                        $record->logs()->delete();
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageServiceProccesses::route('/'),
            'details' => ServiceDetails::route('/{record}/details')
        ];
    }
}
