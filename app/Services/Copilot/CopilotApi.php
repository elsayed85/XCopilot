<?php

namespace App\Services\Copilot;

use App\Models\GithubAccount;
use App\Services\Copilot\Core\CompletionRequest;
use App\Services\Copilot\Traits\HasFinalString;
use App\Services\Copilot\Traits\HasQuery;
use App\Services\Copilot\Traits\HasRules;
use App\Services\Copilot\Traits\HasStream;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CopilotApi
{
    use HasFinalString , HasQuery , HasRules , HasStream;

    private int $delay = 10000;

    /**
     * @throws \Exception
     */
    public function __construct(GithubAccount $githubAccount)
    {
        $this->setGithub($githubAccount);
        $this->getOrRefreshToken();
        $this->setPromptFromArray(config('github-copilot-chat.rules'));
    }

    /**
     * @throws \Exception
     */
    public function send(): string
    {
        if (empty($this->messages)) {
            throw new \Exception('Please add messages to send');
        }

        return $this->getFinalString(
            $this->query(
                completionRequest : new CompletionRequest($this->messages),
                stream : false
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function streamResponse($delay = null): StreamedResponse
    {
        if (empty($this->messages)) {
            throw new \Exception('Please add messages to send');
        }

        $stream = $this->stream(
            $this->query(
                completionRequest : new CompletionRequest($this->messages),
            )
        );

        ray($delay);

        return response()->stream(function () use ($stream) {
            foreach ($stream as $response) {
                usleep($delay ?? $this->delay);
                $text = $response->choices[0]->content;
                if (connection_aborted()) {
                    break;
                }
                echo $text;
                ob_flush();
                flush();
            }

            echo "\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }
}
