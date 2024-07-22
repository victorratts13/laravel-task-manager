<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UpdaterResource\Pages;
use App\Filament\Resources\UpdaterResource\RelationManagers;
use App\Models\Updater;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UpdaterResource extends Resource
{
    protected static ?string $model = Updater::class;

    protected static ?string $navigationIcon = 'heroicon-c-arrow-path';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('version')->disabled(),
                TextInput::make('node')->disabled(),
                TextInput::make('hash')->readOnly(),
                TextInput::make('repository')->prefixIcon('bi-git')->disabled()->columnSpanFull(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('version')->badge()->color('primary'),
                TextColumn::make('node')->icon('heroicon-m-hashtag'),
                TextColumn::make('repository')->icon('bi-git')->url(function ($record) {
                    return $record->repository;
                })->limit(30)->openUrlInNewTab(),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('See')->icon('bi-eye'),
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
            'index' => Pages\ManageUpdaters::route('/'),
        ];
    }
}
