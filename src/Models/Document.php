<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Enums\DocumentStatus;
use Doinc\PersonaKyc\Enums\GovernmentIdClass;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\VerificationStatus;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class Document implements IPersonaModel
{
    use PersonaModel;

    public readonly string $id;
    public readonly DocumentStatus $status;
    public readonly ?Carbon $created_at;
    public readonly ?Carbon $processed_at;
    public readonly ?DocumentPhoto $front_photo;
    public readonly ?DocumentPhoto $back_photo;
    public readonly ?DocumentPhoto $selfie_photo;
    public readonly GovernmentIdClass $id_class;
    public readonly ?string $endorsements;
    public readonly ?string $restrictions;
    public readonly ?string $vehicle_class;
    public readonly DocumentRelations $relationships;

    private function __construct(array $arr)
    {
        $this->id = Arr::get($arr, "data.id");
        $this->status = DocumentStatus::from(Arr::get($arr, "data.attributes.status"));
        $this->created_at = Carbon::parse(Arr::get($arr, "data.attributes.created_at"));
        $this->processed_at = Carbon::parse(Arr::get($arr, "data.attributes.submitted_at"));
        $this->front_photo = Arr::get($arr, "data.attributes.front_photo_url");
        $this->back_photo = Arr::get($arr, "data.attributes.back_photo_url");
        $this->selfie_photo = Arr::get($arr, "data.attributes.selfie_photo_url");
        $this->id_class = GovernmentIdClass::from(Arr::get($arr, "data.attributes.id_class"));
        $this->endorsements = Arr::get($arr, "data.attributes.endorsements");
        $this->restrictions = Arr::get($arr, "data.attributes.restrictions");
        $this->vehicle_class = Arr::get($arr, "data.attributes.vehicle_class");

        $this->relationships = DocumentRelations::from($arr);
    }

    private static function requiredKeys(): array
    {
        return [
            "data.type",
            "data.id",
            "data.attributes",
            "data.attributes.status",
            "data.attributes.created_at",
            "data.attributes.processed_at",
            "data.attributes.front_photo",
            "data.attributes.back_photo",
            "data.attributes.selfie_photo",
            "data.attributes.id_class",
            "data.attributes.endorsements",
            "data.attributes.restrictions",
            "data.attributes.vehicle_class",
            "data.relationships.inquiry",
            "data.relationships.document_files",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): Document
    {
        return self::fromExtended($array, "document/", false);
    }
}
