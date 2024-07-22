<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceProccessResource\Pages;
use App\Filament\Resources\ServiceProccessResource\RelationManagers;
use App\Models\Enviromet;
use App\Models\ServiceProccess;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ServiceProccessResource extends Resource
{
    protected static ?string $model = ServiceProccess::class;

    protected static ?string $navigationIcon = 'heroicon-c-command-line';

    public static function form(Form $form): Form
    {
        $uuid = (string)Str::uuid();
        
        return $form
            ->schema([
                TextInput::make('command')->columnSpanFull()->prefixIcon('heroicon-c-command-line'),
                Select::make('env')->options(function(){
                    if(auth()->user()->access !== 2){
                        return Enviromet::all()->pluck('name', 'id');

                    } else {
                        return Enviromet::where('user', auth()->user()->id)->get()->pluck('name', 'id');
                    }
                })->prefixIcon('heroicon-c-cog'),
                TextInput::make('interval')->numeric()->default(60)->prefixIcon('heroicon-s-clock'),
                Select::make('loggable')->options(['no', 'yes'])->default(1)->prefixIcon('heroicon-m-bars-3-center-left'),
                TextInput::make('tag')->afterStateUpdated(function($state){
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
            ->columns([
                ToggleColumn::make('status'),
                TextColumn::make('pid')->label('PID')->icon('heroicon-c-list-bullet'),
                TextColumn::make('command')->icon('heroicon-c-command-line')->limit(30),
                TextColumn::make('tag')->badge('primary')->icon('heroicon-s-tag'),
                TextColumn::make('uuid')->limit(20)->icon('heroicon-c-link'),
                TextColumn::make('interval')->icon('heroicon-s-clock')->suffix(' Sec.'),
                TextColumn::make('last_execution')->dateTime('d/m/Y H:i')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function ($record) {
                    $record->logs()->delete();
                }),
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
        ];
    }
}
