<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyGithubAccountResource\Pages;
use App\Filament\User\Resources\MyGithubAccountResource\RelationManagers\InvitesRelationManager;
use App\Filament\User\Resources\MyGithubAccountResource\RelationManagers\MembersRelationManager;
use App\Models\GithubAccount;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class MyGithubAccountResource extends Resource
{
    protected static ?string $model = GithubAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return auth()->user()->githubAccounts()->getQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('account_name')
                    ->label('Account Name')
                    ->autofocus(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_name')
                    ->label('Account Name'),

                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('Add Account')
                    ->icon('heroicon-o-plus-circle')
                    ->modalSubmitActionLabel('Confirm Auth ?')
                    ->steps([
                        Step::make('account_name')
                            ->label('Account Name')
                            ->description('the name of the account')
                            ->schema([
                                TextInput::make('account_name')
                                    ->label('Account Name')
                                    ->default(auth()->user()->name)
                                    ->autofocus(),
                            ]),

                        Step::make('github_auth_code')
                            ->label('Github Auth Code')
                            ->description('use this code to authenticate your github account with copilot')
                            ->schema([
                                TextInput::make('github_auth_code')
                                    ->label('Github Auth Code')
                                    ->helperText(new HtmlString('<a href="https://github.com/login/device" target="_blank">Open Github Device Login</a>'))
                                    ->default(function () {
                                        $github = new \App\Services\Copilot\Github();
                                        $github->generateToken();
                                        $code = $github->getUserCode();
                                        $device_code = $github->getDeviceCode();

                                        session(['device_code' => $device_code]);

                                        return $code;
                                    })
                                    ->autofocus(),
                            ]),
                    ])
                    ->after(function (Action $action) {
                        $github = new \App\Services\Copilot\Github();
                        if (! $github->confirm(session('device_code'))) {
                            Notification::make()
                                ->title('Github Authentication Failed')
                                ->body('Please try again')
                                ->danger()
                                ->send();

                            $action->cancel();

                            return false;
                        }

                        auth()->user()->githubAccounts()->create([
                            'account_name' => $action->getFormData()['account_name'],
                            'github_token' => $github->getAccessToken(),
                        ]);

                        Notification::make()
                            ->title('Github Account Added')
                            ->body('Your github account has been added')
                            ->send();

                        $action->close();

                        return true;
                    }),
            ])
            ->actions([
                Action::make('re-generate')
                    ->label('ReAuthenticate')
                    ->modalSubmitActionLabel('Confirm Auth')
                    ->steps([
                        Step::make('github_auth_code')
                            ->label('Github Auth Code')
                            ->description('use this code to authenticate your github account with copilot')
                            ->schema([
                                TextInput::make('github_auth_code')
                                    ->label('Github Auth Code')
                                    ->helperText(new HtmlString('<a href="https://github.com/login/device" target="_blank">Open Github Device Login</a>'))
                                    ->default(function () {
                                        $github = new \App\Services\Copilot\Github();
                                        $github->generateToken();
                                        $code = $github->getUserCode();
                                        $device_code = $github->getDeviceCode();

                                        session(['device_code' => $device_code]);

                                        return $code;
                                    })
                                    ->autofocus(),
                            ]),
                    ])
                    ->after(function ($action, GithubAccount $record) {
                        $github = new \App\Services\Copilot\Github();
                        if (! $github->confirm(session('device_code'))) {
                            Notification::make()
                                ->title('Github Authentication Failed')
                                ->body('Please try again')
                                ->danger()
                                ->send();

                            $action->cancel();

                            return false;
                        }

                        $record->update([
                            'github_token' => $github->getAccessToken(),
                        ]);

                        Notification::make()
                            ->title('Github Account Updated')
                            ->body('Your github account has been updated')
                            ->send();

                        $action->close();

                        return true;
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
            MembersRelationManager::class,
            InvitesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyGithubAccounts::route('/'),
            'view' => Pages\ViewMyGithubAccount::route('/{record}'),
            'edit' => Pages\EditMyGithubAccount::route('/{record}/edit'),
        ];
    }
}
