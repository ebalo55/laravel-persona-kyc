<?php

namespace Doinc\PersonaKyc\Models\RelationMiniModel;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Models\IPersonaModel;

class VerificationMini implements IPersonaModel
{
    use PersonaMiniModel;

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): VerificationMini
    {
        return self::fromExtended(["data" => $array], null, false);
    }
}
