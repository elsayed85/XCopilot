<?php

namespace App\Services\Copilot\Traits;

trait HasFinalString
{
    private function getFinalString(string $data): string
    {
        $dataset = explode("\n", $data);
        $final_string = '';
        foreach ($dataset as $str) {
            $line = substr($str, 6);
            if (str_contains($str, 'data: ')) {

                $responseData = json_decode($line, true);

                $choices = $responseData['choices'] ?? null;

                if ($choices) {
                    foreach ($choices as $choice) {
                        $delta = $choice['delta'] ?? null;

                        if ($delta) {
                            $final_string .= $delta['content'] ?? '';
                        }
                    }
                }
            }
        }

        return $final_string;
    }
}
