<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Enums\GovernmentIdClass;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\VerificationStatus;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class Verification implements IPersonaModel
{
    use PersonaModel;

    public readonly string $id;
    public readonly VerificationStatus $status;
    public readonly ?Carbon $created_at;
    public readonly int $created_at_ts;
    public readonly ?Carbon $submitted_at;
    public readonly int $submitted_at_ts;
    public readonly ?Carbon $completed_at;
    public readonly int $completed_at_ts;
    public readonly ?string $country_code;
    public readonly ?float $entity_confidence_score;
    public readonly ?float $document_similarity_score;
    public readonly ?float $selfie_similarity_score_left;
    public readonly ?float $selfie_similarity_score_right;
    public readonly array $entity_confidence_reasons;
    public readonly ?string $front_photo_url;
    public readonly ?string $back_photo_url;
    public readonly ?string $left_photo_url;
    public readonly ?string $center_photo_url;
    public readonly ?string $right_photo_url;
    /**
     * @var VerificationPhoto[]|null
     */
    public readonly ?array $photo_urls;
    public readonly ?string $selfie_photo_url;
    public readonly GovernmentIdClass $id_class;
    public readonly string $capture_method;
    public readonly ?string $endorsements;
    public readonly ?string $restrictions;
    public readonly ?string $vehicle_class;
    /**
     * @var VerificationCheck[]|null
     */
    public readonly ?array $checks;
    public readonly VerificationRelations $relationships;

    private function __construct(array $arr)
    {
        $this->id = Arr::get($arr, "data.id");
        $this->status = VerificationStatus::from(Arr::get($arr, "data.attributes.status"));
        $this->created_at = Carbon::parse(Arr::get($arr, "data.attributes.created_at"));
        $this->created_at_ts = (int)Arr::get($arr, "data.attributes.created_at_ts");
        $this->submitted_at = Carbon::parse(Arr::get($arr, "data.attributes.submitted_at"));
        $this->submitted_at_ts = (int)Arr::get($arr, "data.attributes.submitted_at_ts");
        $this->completed_at = Carbon::parse(Arr::get($arr, "data.attributes.completed_at"));
        $this->completed_at_ts = (int)Arr::get($arr, "data.attributes.completed_at_ts");
        $this->country_code = Arr::get($arr, "data.attributes.country_code");
        $this->entity_confidence_score = Arr::has($arr, "data.attributes.entity_confidence_score") ?
            round((float)Arr::get($arr, "data.attributes.entity_confidence_score"), 2) : null;
        $this->document_similarity_score = Arr::has($arr, "data.attributes.document_similarity_score") ?
            round((float)Arr::get($arr, "data.attributes.document_similarity_score"), 2) : null;
        $this->selfie_similarity_score_left = Arr::has($arr, "data.attributes.selfie_similarity_score_left") ?
            round((float)Arr::get($arr, "data.attributes.selfie_similarity_score_left"), 2) : null;
        $this->selfie_similarity_score_right = Arr::has($arr, "data.attributes.selfie_similarity_score_right") ?
            round((float)Arr::get($arr, "data.attributes.selfie_similarity_score_right"), 2) : null;
        $this->entity_confidence_reasons = Arr::get($arr, "data.attributes.entity_confidence_reasons");
        $this->front_photo_url = Arr::get($arr, "data.attributes.front_photo_url");
        $this->back_photo_url = Arr::get($arr, "data.attributes.back_photo_url");
        $this->left_photo_url = Arr::get($arr, "data.attributes.left_photo_url");
        $this->center_photo_url = Arr::get($arr, "data.attributes.center_photo_url");
        $this->right_photo_url = Arr::get($arr, "data.attributes.right_photo_url");

        $elem = Arr::get($arr, "data.attributes.photo_urls");
        $this->photo_urls = !is_null($elem) && Arr::has($elem, "page") && count($elem[0]) > 0 ?
            array_map(function ($v) {
                return VerificationPhoto::from($v);
            }, $elem) :
            null;

        $this->selfie_photo_url = Arr::get($arr, "data.attributes.selfie_photo_url");
        $this->id_class = GovernmentIdClass::from(Arr::get($arr, "data.attributes.id_class"));
        $this->capture_method = Arr::get($arr, "data.attributes.capture_method");
        $this->endorsements = Arr::get($arr, "data.attributes.endorsements");
        $this->restrictions = Arr::get($arr, "data.attributes.restrictions");
        $this->vehicle_class = Arr::get($arr, "data.attributes.vehicle_class");

        $elem = Arr::get($arr, "data.attributes.checks");
        $this->checks = !is_null($elem) && count($elem) > 0 && Arr::has($elem[0], "name") && count($elem[0]) > 0 ?
            array_map(function ($v) {
                return VerificationCheck::from($v);
            }, $elem) :
            null;

        $this->relationships = VerificationRelations::from($arr);
    }

    private static function requiredKeys(): array
    {
        return [
            "data.type",
            "data.id",
            "data.attributes",
            "data.attributes.status",
            "data.attributes.created_at",
            "data.attributes.created_at_ts",
            "data.attributes.submitted_at",
            "data.attributes.submitted_at_ts",
            "data.attributes.completed_at",
            "data.attributes.completed_at_ts",
            "data.attributes.country_code",
            // "data.attributes.entity_confidence_score", in some responses do not exists
            "data.attributes.entity_confidence_reasons",
            // "data.attributes.front_photo_url", in some responses do not exists
            // "data.attributes.back_photo_url", in some responses do not exists
            "data.attributes.photo_urls",
            // "data.attributes.selfie_photo_url",  in some responses do not exists
            // "data.attributes.id_class",  in some responses do not exists
            "data.attributes.capture_method",
            // "data.attributes.endorsements",  in some responses do not exists
            // "data.attributes.restrictions",  in some responses do not exists
            // "data.attributes.vehicle_class",  in some responses do not exists
            "data.attributes.checks",
            "data.relationships.inquiry",
            // "data.relationships.document",  in some responses do not exists
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): Verification
    {
        return self::fromExtended($array, "verification/", false);
    }
}
