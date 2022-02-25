<?php

namespace Doinc\PersonaKyc\Models\RelationMiniModel;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Models\IPersonaModel;

class InquiryTemplateVersionMini implements IPersonaModel
{
    use PersonaMiniModel;

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): InquiryTemplateVersionMini
    {
        return self::fromExtended($array, "inquiry-template-version");
    }
}
