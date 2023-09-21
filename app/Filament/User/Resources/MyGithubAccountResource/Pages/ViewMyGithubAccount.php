<?php

namespace App\Filament\User\Resources\MyGithubAccountResource\Pages;

use App\Filament\User\Resources\MyGithubAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyGithubAccount extends ViewRecord
{
    protected static string $resource = MyGithubAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
