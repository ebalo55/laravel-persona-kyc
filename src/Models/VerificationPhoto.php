<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Enums\GovernmentIdClass;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\VerificationStatus;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class VerificationPhoto implements IPersonaModel
{
    use PersonaModel;

    public readonly string $page;
    public readonly string $url;
    public readonly ?string $normalized_url;

    private function __construct(array $arr)
    {
        $this->page = Arr::get($arr, "page");
        $this->url = Arr::get($arr, "url");
        $this->normalized_url = Arr::get($arr, "normalized_url");
    }

    private static function requiredKeys(): array
    {
        return [
            "page",
            "url",
            // "normalized_url", in some responses do not exists
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): VerificationPhoto
    {
        return self::fromExtended($array, null, false);
    }
}
