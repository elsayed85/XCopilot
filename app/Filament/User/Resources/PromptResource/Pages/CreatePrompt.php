<?php

namespace App\Filament\User\Resources\PromptResource\Pages;

use App\Filament\User\Resources\PromptResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrompt extends CreateRecord
{
    protected static string $resource = PromptResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
