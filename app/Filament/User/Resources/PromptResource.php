<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PromptResource\Pages;
use App\Filament\User\Resources\PromptResource\RelationManagers\RulesRelationManager;
use App\Models\Prompt;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromptResource extends Resource
{
    protected static ?string $model = Prompt::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->autofocus()
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('default')
                    ->updateStateUsing(function ($record, $state) {
                        if ($state) {
                            auth()->user()->prompts()->update('default', true)->update(['default' => false]);
                        }

                        $record->update(['default' => $state]);
                    })
                    ->label('Default Prompt')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrompts::route('/'),
            'create' => Pages\CreatePrompt::route('/create'),
            'edit' => Pages\EditPrompt::route('/{record}/edit'),
        ];
    }
}
