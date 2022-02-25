<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class Metadata implements IPersonaModel
{
    use PersonaModel;

    public readonly ?string $session_token;

    private function __construct(array $arr)
    {
        $this->session_token = Arr::has($arr, "meta.session_token") ? Arr::get($arr, "meta.session_token") : null;
    }

    private static function requiredKeys(): array
    {
        return [
            "meta",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): Metadata
    {
        return self::fromExtended($array, null, false);
    }
}
