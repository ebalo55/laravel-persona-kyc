<?php

namespace Doinc\PersonaKyc\Models\RelationMiniModel;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Models\IPersonaModel;

class SelfieMini implements IPersonaModel
{
    use PersonaMiniModel;

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): SelfieMini
    {
        return self::fromExtended(["data" => $array], null, false);
    }
}
