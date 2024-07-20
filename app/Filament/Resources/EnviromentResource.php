<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnviromentResource\Pages;
use App\Filament\Resources\EnviromentResource\RelationManagers;
use App\Forms\Components\InternalPath;
use App\Models\Enviromet as Enviroment;
use App\Models\Enviromet;
use App\Models\Queues;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class EnviromentResource extends Resource
{
    protected static ?string $model = Enviroment::class;

    protected static ?string $navigationIcon = 'heroicon-c-cog';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->columnSpanFull(),
                Select::make('user')->options(function () {
                    return User::all()->pluck('name', 'id');
                })->searchable()->native(false),
                Select::make('queue')->options(function () {
                    return Queues::all()->pluck('name', 'id');
                })->searchable()->native(false),
                Select::make('path')
                    ->prefixIcon('heroicon-c-folder')
                    ->native(true)
                    ->columnSpanFull()
                    ->searchable()
                    ->searchDebounce(debounce: 500)
                    ->getSearchResultsUsing(function ($search) {
                        if ($search) {
                            // dd($search);
                            if (File::isDirectory($search)) {
                                $directories = collect(File::directories($search))->filter(function ($mp) use ($search) {
                                    return is_numeric(strpos($mp, $search));
                                })->map(function ($mp, $key) {
                                    return [
                                        'id' => $key,
                                        'path' => $mp,
                                        'tree' => explode('/', $mp)
                                    ];
                                })->pluck('path', 'path');
                            } else {
                                $directories = collect(File::directories("/"))->map(function ($mp, $key) {
                                    return [
                                        'id' => $key,
                                        'path' => $mp,
                                        'tree' => explode('/', $mp)
                                    ];
                                })->pluck('path', 'path');
                            }
                            return $directories;
                        } else {
                            return collect(File::directories("/"))->map(function ($mp, $key) {
                                return [
                                    'id' => $key,
                                    'path' => $mp,
                                    'tree' => explode('/', $mp)
                                ];
                            })->pluck('path', 'path');
                        }
                    })
                    ->getOptionLabelUsing(function ($value) {
                        if ($value) {
                            if (File::isDirectory($value)) {
                                return $value;
                            }
                        } else {
                            return "/";
                        }
                        // return ['teste', 'teste02'];
                    })
                    ->options(function($state){
                        if ($state) {
                            if (File::isDirectory($state)) {
                                $directories = collect(File::directories($state))->filter(function ($mp) use ($state) {
                                    return is_numeric(strpos($mp, $state));
                                })->map(function ($mp, $key) {
                                    return [
                                        'id' => $key,
                                        'path' => $mp,
                                        'tree' => explode('/', $mp)
                                    ];
                                })->pluck('path', 'path');
                            } else {
                                $directories = collect(File::directories("/"))->map(function ($mp, $key) {
                                    return [
                                        'id' => $key,
                                        'path' => $mp,
                                        'tree' => explode('/', $mp)
                                    ];
                                })->pluck('path', 'path');
                            }
                            return $directories;
                        } else {
                            return collect(File::directories("/"))->map(function ($mp, $key) {
                                return [
                                    'id' => $key,
                                    'path' => $mp,
                                    'tree' => explode('/', $mp)
                                ];
                            })->pluck('path', 'path');
                        }
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('status'),
                TextColumn::make('name')->icon('heroicon-c-code-bracket-square'),
                TextColumn::make('user')->getStateUsing(function ($record) {
                    return $record->user()->first()->name;
                })->icon('heroicon-c-user'),
                TextColumn::make('path')->icon('heroicon-s-folder-open')->limit(20),
                TextColumn::make('queue')->icon('heroicon-s-server')->getStateUsing(function ($record) {
                    return $record->queue()->first()->name;
                }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageEnviroments::route('/'),
        ];
    }
}
