<?php

namespace App\Filament\User\Resources\MyGithubAccountResource\Pages;

use App\Filament\User\Resources\MyGithubAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyGithubAccount extends EditRecord
{
    protected static string $resource = MyGithubAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
