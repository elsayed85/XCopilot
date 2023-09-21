<?php

namespace App\Filament\User\Resources\MyGithubAccountResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InvitesRelationManager extends RelationManager
{
    protected static string $relationship = 'invites';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->columnSpanFull()
                    ->password()
                    ->helperText('The token that will be used to redeem this invite.')
                    ->visibleOn('view'),

                Forms\Components\TextInput::make('usages')
                    ->label('Usages')
                    ->visibleOn('view'),

                Forms\Components\TextInput::make('max_usages')
                    ->label('Max Usages')
                    ->helperText('How many times this invite can be used.')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(10),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->helperText('When this invite expires. Leave blank for no expiry.')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('usages')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),

                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->copyable()
                    ->copyMessage('Copied to clipboard!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('usages')
                    ->label('Usages'),

                Tables\Columns\TextColumn::make('max_usages')
                    ->label('Max Usages'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
