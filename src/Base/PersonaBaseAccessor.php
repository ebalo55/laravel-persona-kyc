<?php

namespace Doinc\PersonaKyc\Base;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;
use function config;

class PersonaBaseAccessor
{
    protected string $api_key;

    protected function __construct()
    {
        $this->api_key = config("persona-kyc.api_key");
    }

    protected function log(string $name, string $content)
    {
        file_put_contents("$name.json", $content);
    }

    protected function extraVerboseLog(string $endpoint, array $parameters, string $log_name, string|array $content)
    {
        // behave just like normal log
        if (!is_array($content)) {
            file_put_contents("$log_name.json", $content);
        } else {
            file_put_contents("$log_name.json", json_encode([
                "extra" => [
                    "endpoint" => $endpoint,
                    "parameters" => $parameters
                ],
                ...$content
            ]));
        }
    }

    #[ArrayShape(["Content-Type" => "string", "Authorization" => "string", "Key-Inflection" => "string"])]
    protected function headers(): array
    {
        return [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer {$this->api_key}",
            "Key-Inflection" => "snake"
        ];
    }

    protected function baseRequest(): PendingRequest
    {
        return Http::withHeaders($this->headers());
    }
}
