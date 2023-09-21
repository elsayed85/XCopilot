<?php

namespace App\Services\Copilot\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

trait HasToken
{
    use HasGithubToken;

    private ?string $token;

    private $tokenExpiresAt;

    private function getToken(): ?string
    {
        return $this->token;
    }

    public function getTokenExpiresAt(): ?Carbon
    {
        return $this->tokenExpiresAt;
    }

    private function generateToken()
    {
        $response = Http::withHeaders([
            'User-Agent' => config('github-copilot-chat.user_agent'),
        ])
            ->withToken($this->getGithubToken())
            ->get('https://api.github.com/copilot_internal/v2/token');

        if ($response->status() != 200) {
            throw new \Exception('Could not generate copilot token : '.$response->body());
        }

        return $response->json();
    }

    private function shouldGenerateNewToken(): bool
    {
        if (! $this->token || ! $this->tokenExpiresAt) {
            return true;
        }

        return $this->getTokenExpiresAt()->isPast();
    }

    /**
     * @throws \Exception
     */
    private function getOrRefreshToken(): void
    {
        $githubAccount = $this->getGithubAccount();

        $this->token = $githubAccount->getCopilotToken();
        $this->tokenExpiresAt = $githubAccount->getCopilotTokenExpiresAt();

        if ($this->shouldGenerateNewToken()) {
            $response = $this->generateToken();

            if (! isset($response['token']) || ! isset($response['expires_at'])) {
                throw new \Exception('Could not generate token');
            }

            $this->token = $response['token'];
            $this->tokenExpiresAt = $response['expires_at'];

            $githubAccount->update([
                'copilot_token' => $this->token,
                'copilot_token_expires_at' => $this->tokenExpiresAt,
            ]);
        }

    }
}
