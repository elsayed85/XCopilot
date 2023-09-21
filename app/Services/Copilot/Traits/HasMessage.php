<?php

namespace App\Services\Copilot\Traits;

use App\Services\Copilot\Core\Message;
use App\Services\Copilot\Core\WebSearch;

trait HasMessage
{
    private array $messages = [];

    public function addMessage($message, $role = 'user', $webSearch = false): static
    {
        $this->messages[] = new Message($message, $role);

        if ($webSearch) {
            $copilotMessage = (new WebSearch())->fetch($message)->asCopilotMessage();

            if ($copilotMessage) {
                $this->messages[] = $copilotMessage;
            }
        }

        return $this;
    }

    public function setMessages(array $messages): static
    {
        foreach ($messages as $message) {
            $this->addMessage($message['content'], $message['role'], $message['webSearch'] ?? false);
        }

        return $this;
    }
}
