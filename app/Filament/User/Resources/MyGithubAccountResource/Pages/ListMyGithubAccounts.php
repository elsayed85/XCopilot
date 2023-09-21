<?php

namespace App\Filament\User\Resources\MyGithubAccountResource\Pages;

use App\Filament\User\Resources\MyGithubAccountResource;
use Filament\Resources\Pages\ListRecords;

class ListMyGithubAccounts extends ListRecords
{
    protected static string $resource = MyGithubAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
