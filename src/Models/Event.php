<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Enums\EventTypes;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;
use SodiumException;

class Event implements IPersonaModel
{
    use PersonaModel;

    public readonly string $id;
    public readonly string $created_at;
    public EventTypes $event_type;
    private Account|Inquiry|InquirySession|Document|Verification|array $payload; // missing report, selfie, case, property that have an unknown structure

    private function __construct(array $arr)
    {
        $this->id = Arr::get($arr, "data.id");
        $this->event_type = EventTypes::from(Arr::get($arr, "data.attributes.name"));
        $this->payload = Arr::get($arr, "data.attributes.payload");
        $this->created_at = Arr::get($arr, "data.attributes.created_at");
    }

    private static function requiredKeys(): array
    {
        return [
            "data",
            "data.type",
            "data.id",
            "data.attributes",
            "data.attributes.name",
            "data.attributes.payload",
            "data.attributes.payload.data.type",
            "data.attributes.created_at",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): Event
    {
        return self::fromExtended($array, "event");
    }

    private function eventMapping(): array {
        return [
            EventTypes::ACCOUNT_CREATED->value => Account::class,
            EventTypes::ACCOUNT_REDACTED->value => Account::class,
            EventTypes::ACCOUNT_ARCHIVED->value => Account::class,
            EventTypes::ACCOUNT_RESTORED->value => Account::class,
            EventTypes::ACCOUNT_CONSOLIDATED->value => Account::class,
            EventTypes::ACCOUNT_TAG_ADDED->value => Account::class,
            EventTypes::ACCOUNT_TAG_REMOVED->value => Account::class,

            EventTypes::CASE_CREATED->value => null,
            EventTypes::CASE_ASSIGNED->value => null,
            EventTypes::CASE_RESOLVED->value => null,
            EventTypes::CASE_REOPENED->value => null,
            EventTypes::CASE_UPDATED->value => null,

            EventTypes::DOCUMENT_CREATED->value => Document::class,
            EventTypes::DOCUMENT_SUBMITTED->value => Document::class,
            EventTypes::DOCUMENT_PROCESSED->value => Document::class,
            EventTypes::DOCUMENT_ERRORED->value => Document::class,

            EventTypes::INQUIRY_CREATED->value => Inquiry::class,
            EventTypes::INQUIRY_STARTED->value => Inquiry::class,
            EventTypes::INQUIRY_EXPIRED->value => Inquiry::class,
            EventTypes::INQUIRY_COMPLETED->value => Inquiry::class,
            EventTypes::INQUIRY_FAILED->value => Inquiry::class,
            EventTypes::INQUIRY_MARKED_FOR_REVIEW->value => Inquiry::class,
            EventTypes::INQUIRY_APPROVED->value => Inquiry::class,
            EventTypes::INQUIRY_DECLINED->value => Inquiry::class,

            EventTypes::INQUIRY_SESSION_STARTED->value => InquirySession::class,
            EventTypes::INQUIRY_SESSION_EXPIRED->value => InquirySession::class,

            EventTypes::REPORT_ADDRESS_LOOKUP_READY->value => null,
            EventTypes::REPORT_ADVERSE_MEDIA_MATCHED->value => null,
            EventTypes::REPORT_ADVERSE_MEDIA_READY->value => null,
            EventTypes::REPORT_BUSINESS_ADVERSE_MEDIA_MATCHED->value => null,
            EventTypes::REPORT_BUSINESS_ADVERSE_MEDIA_READY->value => null,
            EventTypes::REPORT_BUSINESS_WATCHLIST_READY->value => null,
            EventTypes::REPORT_BUSINESS_WATCHLIST_MATCHED->value => null,
            EventTypes::REPORT_EMAIL_ADDRESS_READY->value => null,
            EventTypes::REPORT_PHONE_NUMBER_READY->value => null,
            EventTypes::REPORT_PROFILE_READY->value => null,
            EventTypes::REPORT_WATCHLIST_MATCHED->value => null,
            EventTypes::REPORT_WATCHLIST_READY->value => null,

            EventTypes::SELFIE_CREATED->value => null,
            EventTypes::SELFIE_SUBMITTED->value => null,
            EventTypes::SELFIE_PROCESSED->value => null,
            EventTypes::SELFIE_ERRORED->value => null,

            EventTypes::VERIFICATION_CREATED->value => Verification::class,
            EventTypes::VERIFICATION_SUBMITTED->value => Verification::class,
            EventTypes::VERIFICATION_PASSED->value => Verification::class,
            EventTypes::VERIFICATION_FAILED->value => Verification::class,
            EventTypes::VERIFICATION_REQUIRES_RETRY->value => Verification::class,
            EventTypes::VERIFICATION_CANCELED->value => Verification::class,
            EventTypes::ACCOUNT_PROPERTY_REDACTED->value => Verification::class,
        ];
    }

    /**
     * Get the object representation of the event if available
     *
     * @return Account|Inquiry|InquirySession|Document|Verification|array
     */
    public function payload(): Account|Inquiry|InquirySession|Document|Verification|array {
        return !is_null($this->eventMapping()[$this->event_type->value]) ?
            ($this->eventMapping()[$this->event_type->value])::from($this->payload) : $this->payload;
    }

    /**
     * Hash the current event using BLAKE2b and returns 512 bits long hash.
     *
     * Signatures can be used to avoid duplicated events handling storing a simple hash value into the database
     * instead of having to store a full event record.
     *
     * NOTE: the size of the resulting hash can be modified overriding the default SODIUM_CRYPTO_GENERICHASH_BYTES_MAX
     *      value, supported sizes are 256 & 512 bits
     *
     * @return string
     * @throws SodiumException
     */
    public function signature(): string {
        return sodium_bin2hex(sodium_crypto_generichash(
            Event::class . "|{$this->created_at}|{$this->event_type}|" . json_encode($this->payload),
            length: SODIUM_CRYPTO_GENERICHASH_BYTES_MAX
        ));
    }
}
