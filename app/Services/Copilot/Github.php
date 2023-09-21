<?php

namespace App\Services\Copilot;

use Illuminate\Support\Facades\Http;

class Github
{
    protected string $user_code;

    protected string $device_code;

    protected string $access_token;

    public function getUserCode(): string
    {
        return $this->user_code;
    }

    public function getDeviceCode(): string
    {
        return $this->device_code;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function generateToken(): static
    {
        $response = Http::asForm()->post(
            'https://github.com/login/device/code',
            [
                'client_id' => config('github-copilot-chat.client_id'),
                'scope' => 'user:email',
            ]
        )->body();

        $response = explode('&', $response);
        $user_code = explode('=', $response[3])[1];
        $device_code = explode('=', $response[0])[1];

        $this->user_code = $user_code;
        $this->device_code = $device_code;

        return $this;
    }

    public function confirm($device_code): bool
    {
        $response = Http::asForm()->post(
            'https://github.com/login/oauth/access_token',
            [
                'client_id' => config('github-copilot-chat.client_id'),
                'scope' => 'user:email',
                'device_code' => $device_code,
                'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
            ]
        );

        $response = explode('&', $response->body());

        $resp = explode('=', $response[0]);
        $key = $resp[0] ?? null;
        $access_token = $resp[1] ?? null;

        if (! $access_token) {
            return false;
        }

        if ($key == 'error') {
            return false;
        }

        if ($access_token == 'authorization_pending') {
            return false;
        }

        $this->access_token = $access_token;

        return true;
    }
}
