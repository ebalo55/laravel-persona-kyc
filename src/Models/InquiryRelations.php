<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Models\RelationMiniModel\AccountMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\DocumentMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\InquiryTemplateMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\InquiryTemplateVersionMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\SelfieMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\SessionMini;
use Doinc\PersonaKyc\Models\RelationMiniModel\VerificationMini;
use Illuminate\Support\Arr;

class InquiryRelations implements IPersonaModel
{
    use PersonaModel;

    public readonly AccountMini $account;
    public readonly ?array $template;
    public readonly InquiryTemplateMini $inquiry_template;
    public readonly InquiryTemplateVersionMini $inquiry_template_version;
    public readonly ?array $reviewer;
    public readonly ?array $reports;
    /**
     * @var VerificationMini[]|null
     */
    public readonly ?array $verifications;
    /**
     * @var SessionMini[]|null
     */
    public readonly ?array $sessions;
    /**
     * @var DocumentMini[]|null
     */
    public readonly ?array $documents;
    /**
     * @var SelfieMini[]|null
     */
    public readonly ?array $selfies;

    private function __construct(array $arr)
    {
        $this->account = !is_null(Arr::get($arr, "data.relationships.account")) ?
            AccountMini::from(Arr::get($arr, "data.relationships.account")) : null;

        $this->template = Arr::get($arr, "data.relationships.template");

        $elem = Arr::get($arr, "data.relationships.inquiry_template");
        $this->inquiry_template = !is_null($elem) ? InquiryTemplateMini::from($elem) : null;

        $elem = Arr::get($arr, "data.relationships.inquiry_template_version");
        $this->inquiry_template_version = !is_null($elem) ? InquiryTemplateVersionMini::from($elem) : null;

        $this->reviewer = Arr::get($arr, "data.relationships.reviewer");

        $this->reports = Arr::get($arr, "data.relationships.reports");

        $elem = Arr::get($arr, "data.relationships.verifications");
        $this->verifications = !is_null($elem) && Arr::has($elem, "data") && count(Arr::get($elem, "data")) > 0 ?
            array_map(function ($v) {
                return VerificationMini::from($v);
            }, $elem["data"]) :
            null;

        $elem = Arr::get($arr, "data.relationships.sessions");
        $this->sessions = !is_null($elem) && Arr::has($elem, "data") && count(Arr::get($elem, "data")) > 0 ?
            array_map(function ($v) {
                return SessionMini::from($v);
            }, $elem["data"]) :
            null;

        $elem = Arr::get($arr, "data.relationships.documents");
        $this->documents = !is_null($elem) && Arr::has($elem, "data") && count(Arr::get($elem, "data")) > 0 ?
            array_map(function ($v) {
                return DocumentMini::from($v);
            }, $elem["data"]) :
            null;

        $elem = Arr::get($arr, "data.relationships.selfies");
        $this->selfies = !is_null($elem) && Arr::has($elem, "data") && count(Arr::get($elem, "data")) > 0 ?
            array_map(function ($v) {
                return SelfieMini::from($v);
            }, $elem["data"]) :
            null;
    }

    private static function requiredKeys(): array
    {
        return [
            "data.relationships.account",
            "data.relationships.template",
            "data.relationships.inquiry_template",
            "data.relationships.inquiry_template_version",
            "data.relationships.reviewer",
            "data.relationships.reports",
            "data.relationships.verifications",
            "data.relationships.sessions",
            "data.relationships.documents",
            "data.relationships.selfies",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): InquiryRelations
    {
        return self::fromExtended($array, "inquiry");
    }
}
