<?php

namespace Doinc\PersonaKyc\Models\RelationMiniModel;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Models\IPersonaModel;
use Illuminate\Support\Arr;

class DocumentMini implements IPersonaModel
{
    use PersonaMiniModel;

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): DocumentMini
    {
        if (!Arr::has($array, "data")) {
            $array = ["data" => $array];
        }
        return self::fromExtended($array, null, false);
    }
}
