<?php

namespace App\Filament\User\Widgets;

use App\Models\AccountInvite;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class SharedGithubAccounts extends BaseWidget
{
    protected int|string|array $columnSpan = 12;

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Shared With You';
    }

    protected function getTableQuery(): Builder
    {
        return auth()->user()->sharedGithubAccounts()->getQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('account_name')
                ->label('Account Name'),

            TextColumn::make('members_count')
                ->label('Members')
                ->counts('members'),
        ];
    }

    protected function getTableRecordAction(): ?string
    {
        return 'view';
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('Redeem')
                ->label('Redeem Invite')
                ->requiresConfirmation()
                ->form([
                    TextInput::make('token')
                        ->label('Token')
                        ->required()
                        ->rules([
                            'exists:account_invites,token',
                        ]),
                ])
                ->action(function ($data) {
                    $invite = AccountInvite::where('token', $data['token'])->firstOrFail();

                    // cant add your self to the account you created
                    if ($invite->account->user_id === auth()->user()->id) {
                        Notification::make()
                            ->danger()
                            ->title('Invite Already Redeemed')
                            ->body('You have already redeemed this invite.')
                            ->send();

                        return;
                    }

                    if ($invite->account->members->contains(auth()->user())) {
                        Notification::make()
                            ->danger()
                            ->title('Invite Already Redeemed')
                            ->body('You have already redeemed this invite.')
                            ->send();

                        return;
                    }

                    if ($invite->isExpired()) {
                        Notification::make()
                            ->danger()
                            ->title('Invite Expired')
                            ->body('This invite has expired.')
                            ->send();

                        return;
                    }

                    if ($invite->isMaxedOut()) {
                        Notification::make()
                            ->danger()
                            ->title('Invite Maxed Out')
                            ->body('This invite has been maxed out.')
                            ->send();

                        return;
                    }

                    $invite->redeem();

                    Notification::make()
                        ->success()
                        ->title('Invite Redeemed')
                        ->body('You have successfully redeemed this invite.')
                        ->send();
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->slideOver()
                ->infolist([
                    TextEntry::make('account_name')
                        ->label('Account Name'),
                ]),

            DetachAction::make()
                ->label('Leave')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->members()->detach(auth()->user());

                    Notification::make()
                        ->success()
                        ->title('Left Account')
                        ->body('You have successfully left this account.')
                        ->send();
                }),
        ];
    }
}
