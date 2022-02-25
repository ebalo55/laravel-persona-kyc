<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class Inquiry implements IPersonaModel
{
    use PersonaModel;

    public readonly string $id;
    public readonly InquiryStatus $status;
    public readonly string $reference_id;
    public readonly ?string $note;
    public readonly array $tags;
    public readonly string $creator;
    public readonly ?string $reviewer_comment;
    public readonly Carbon $created_at;
    public readonly ?Carbon $started_at;
    public readonly ?Carbon $completed_at;
    public readonly ?Carbon $failed_at;
    public readonly ?Carbon $decisioned_at;
    public readonly ?Carbon $expired_at;
    public readonly ?Carbon $redacted_at;
    public readonly ?string $previous_step_name;
    public readonly ?string $next_step_name;
    public readonly array $fields;
    public readonly InquiryRelations $relationships;
    public readonly ?Metadata $metadata;

    private function __construct(array $arr)
    {
        $this->id = Arr::get($arr, "data.id");
        $this->status = InquiryStatus::from(Arr::get($arr, "data.attributes.status"));
        $this->reference_id = Arr::get($arr, "data.attributes.reference_id");
        $this->note = Arr::get($arr, "data.attributes.note");
        $this->tags = Arr::get($arr, "data.attributes.tags");
        $this->creator = Arr::get($arr, "data.attributes.creator");
        $this->reviewer_comment = Arr::get($arr, "data.attributes.reviewer_comment");
        $this->created_at = Carbon::parse(Arr::get($arr, "data.attributes.created_at"));
        $this->started_at = Carbon::parse(Arr::get($arr, "data.attributes.started_at"));
        $this->completed_at = Carbon::parse(Arr::get($arr, "data.attributes.completed_at"));
        $this->failed_at = Carbon::parse(Arr::get($arr, "data.attributes.failed_at"));
        $this->decisioned_at = Carbon::parse(Arr::get($arr, "data.attributes.decisioned_at"));
        $this->expired_at = Carbon::parse(Arr::get($arr, "data.attributes.expired_at"));
        $this->redacted_at = Carbon::parse(Arr::get($arr, "data.attributes.redacted_at"));
        $this->previous_step_name = Arr::get($arr, "data.attributes.previous_step_name");
        $this->next_step_name = Arr::get($arr, "data.attributes.next_step_name");
        $this->fields = Arr::get($arr, "data.attributes.fields");
        $this->relationships = InquiryRelations::from($arr);

        // check if meta keys are present and set the values
        if(Arr::has($arr, "meta")) {
            $this->metadata = Metadata::from($arr);
        }
    }

    private static function requiredKeys(): array
    {
        return [
            "data.type",
            "data.id",
            "data.attributes",
            "data.attributes.status",
            "data.attributes.note",
            "data.attributes.tags",
            "data.attributes.creator",
            "data.attributes.fields",
            "data.attributes.reference_id",
            "data.attributes.reviewer_comment",
            "data.attributes.created_at",
            "data.attributes.started_at",
            "data.attributes.completed_at",
            "data.attributes.failed_at",
            "data.attributes.decisioned_at",
            "data.attributes.expired_at",
            "data.attributes.redacted_at",
            "data.attributes.previous_step_name",
            "data.attributes.next_step_name",
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
    public static function from(array $array): Inquiry
    {
        return self::fromExtended($array, "inquiry");
    }
}
