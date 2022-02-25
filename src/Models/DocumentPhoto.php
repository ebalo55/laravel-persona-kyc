<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Enums\GovernmentIdClass;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\VerificationStatus;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class DocumentPhoto implements IPersonaModel
{
    use PersonaModel;

    public readonly string $filename;
    public readonly string $url;

    private function __construct(array $arr)
    {
        $this->filename = Arr::get($arr, "filename");
        $this->url = Arr::get($arr, "url");
    }

    private static function requiredKeys(): array
    {
        return [
            "filename",
            "url",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): DocumentPhoto
    {
        return self::fromExtended($array, null, false);
    }
}
