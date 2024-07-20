<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QueuesResource\Pages;
use App\Filament\Resources\QueuesResource\RelationManagers;
use App\Models\Queues;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QueuesResource extends Resource
{
    protected static ?string $model = Queues::class;

    protected static ?string $navigationIcon = 'heroicon-s-queue-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->columnSpanFull(),
                TextInput::make('memory')->numeric()->prefixIcon('heroicon-s-cpu-chip')->placeholder('In MB'),
                TextInput::make('limit')->numeric()->prefixIcon('heroicon-c-circle-stack')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('status'),
                TextColumn::make('name')->icon('heroicon-s-queue-list')->color('primary'),
                TextColumn::make('memory')->suffix('MB')->color('success')->icon('heroicon-s-cpu-chip'),
                TextColumn::make('limit')->suffix(' Prc')->getStateUsing(function ($record) {
                    if ($record->limit == 0) {
                        return "All";
                    }

                    return $record->limit;
                })->icon('heroicon-c-circle-stack'),
                TextColumn::make('created_at')->label('Created at')->dateTime('d/m/Y H:i')

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
            'index' => Pages\ManageQueues::route('/'),
        ];
    }
}
