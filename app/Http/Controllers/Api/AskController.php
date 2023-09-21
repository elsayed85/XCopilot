<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AskQuestionRequest;
use App\Services\Copilot\CopilotApi;

class AskController extends Controller
{
    /**
     * @throws \Exception
     */
    public function ask(AskQuestionRequest $request)
    {
        if (! $request->authorizeGithubAccount()) {
            return response()->json([
                'error' => 'You are not authorized to use this account',
            ], 403);
        }

        try {
            $copilot = (new CopilotApi($request->getGithubAccount()))
                ->addMessage(
                    message: $request->getQuestion(),
                    webSearch: $request->shouldUseWebSearch()
                );

            if ($request->shouldStream()) {
                return $copilot->streamResponse();
            }

            return response()->json([
                'reply' => $copilot->send(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
