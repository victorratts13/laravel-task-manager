<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceLogsResource\Pages;
use App\Filament\Resources\ServiceLogsResource\RelationManagers;
use App\Models\ServiceLogs;
use App\Models\ServiceProccess;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceLogsResource extends Resource
{
    protected static ?string $model = ServiceLogs::class;

    protected static ?string $navigationIcon = 'heroicon-c-bars-3-center-left';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('command')->disabled()->columnSpanFull(),
                Textarea::make('output')->columnSpanFull()->rows(16)->readOnly()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label('#'),
                TextColumn::make('command')->icon('heroicon-m-command-line')->color('primary')->badge()->searchable(),
                TextColumn::make('service')->getStateUsing(function ($record) {
                    return ServiceProccess::where('id', $record->service)->first()->tag;
                })->icon('heroicon-m-code-bracket-square'),
                TextColumn::make('created_at')->date('d/m/Y H:i')->searchable()
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
            'index' => Pages\ManageServiceLogs::route('/'),
        ];
    }
}
