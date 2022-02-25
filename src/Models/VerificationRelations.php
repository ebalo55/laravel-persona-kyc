<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Models\RelationMiniModel\AccountMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\DocumentMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\InquiryMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\InquiryTemplateMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\InquiryTemplateVersionMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\SelfieMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\SessionMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\VerificationMini;
use Illuminate\Support\Arr;

class VerificationRelations implements IPersonaModel
{
    use PersonaModel;

    public readonly ?InquiryMini $inquiry;
    public readonly ?DocumentMini $document;

    private function __construct(array $arr)
    {
        $elem = Arr::get($arr, "data.relationships.inquiry");
        $this->inquiry = !is_null($elem) ? InquiryMini::from($elem) : null;

        $elem = Arr::get($arr, "data.relationships.document");
        $this->document = !is_null($elem) ? DocumentMini::from($elem) : null;
    }

    private static function requiredKeys(): array
    {
        return [
            "data.relationships.inquiry",
            // "data.relationships.document",  in some responses do not exists
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): VerificationRelations
    {
        return self::fromExtended($array, null, false);
    }
}
