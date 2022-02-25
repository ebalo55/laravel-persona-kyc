<?php

namespace Doinc\PersonaKyc;

use Carbon\Carbon;
use Doinc\PersonaKyc\Base\PersonaBaseAccessor;
use Doinc\PersonaKyc\Base\PersonaBaseInitializer;
use Doinc\PersonaKyc\Base\Redactable;
use Doinc\PersonaKyc\Base\Taggable;
use Doinc\PersonaKyc\Enums\ApiEndpoints;
use Doinc\PersonaKyc\Enums\IPersonaTemplates;
use Doinc\PersonaKyc\Enums\RequestMode;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Exceptions\InvalidNote;
use Doinc\PersonaKyc\Exceptions\InvalidPageSize;
use Doinc\PersonaKyc\Exceptions\InvalidTagName;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
use Doinc\PersonaKyc\Models\Inquiry;
use Doinc\PersonaKyc\Models\PaginatedInquiries;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonaInquiries extends PersonaBaseAccessor
{
    use PersonaBaseInitializer, Redactable, Taggable;

    /**
     * Creates a new inquiry on persona.
     *
     * As inquiry are instances of identity verification performed by a user, creating an inquiry means
     * to request a new identity verification for a given user.
     * In order to use the full power of Persona inquiries will always be created using reference_id, this means
     * that all actions and requests of a user will be grouped in an account leading to a full profile of the user.
     *
     * @param string $reference_id Identifier used by Persona to group all related user's requests and information.
     *                              This should be the unique identifier of the user for easier usage.
     * @param IPersonaTemplates $template_identifier Name of the template used for the inquiry generation, users will
     *                              follow the workflow defined by the given template.
     * @return Inquiry
     * @throws PersonaRecordNotFound
     */
    public function create(string $reference_id, IPersonaTemplates $template_identifier): Inquiry
    {
        $response = $this->baseRequest()->post(ApiEndpoints::INQUIRIES->value, [
            "data" => [
                "type" => "inquiry",
                "attributes" => [
                    Str::startsWith($template_identifier->val(), "i") ? "inquiry-template-id" : "template-id" => $template_identifier->val(),
                    "reference-id" => $reference_id
                ]
            ]
        ]);

        PersonaErrorChecker::checkErrors($response);
        return Inquiry::from($response->json());
    }

    /**
     * Returns a list of inquiries
     *
     * @param string $offset Inquiry id from where to start extracting data
     * @param int $page_size Number of records to return per page
     * @param string $filter_account_id Filter by the provided account id
     * @param string $filter_reference_id Filter by the provided reference id
     * @param bool $offset_inverted Whether the offset is used to get the previous page or the next one
     * @return PaginatedInquiries
     * @throws PersonaRecordNotFound
     * @throws InvalidPageSize
     */
    public function list(
        string $offset = "",
        int    $page_size = 10,
        string $filter_account_id = "",
        string $filter_reference_id = "",
        bool   $offset_inverted = false
    ): PaginatedInquiries
    {
        if ($page_size < 1 || $page_size > 100) {
            throw new InvalidPageSize();
        }

        $response = $this->baseRequest()->get(ApiEndpoints::INQUIRIES->value, [
            "page" => [
                "before" => $offset_inverted ? $offset : "",
                "after" => !$offset_inverted ? $offset : "",
                "size" => "$page_size"
            ],
            "filter" => [
                "reference-id" => $filter_reference_id,
                "account-id" => $filter_account_id,
            ]
        ]);

        PersonaErrorChecker::checkErrors($response);
        return PaginatedInquiries::from($response->json());
    }

    /**
     * Returns the inquiry identified by given id
     *
     * @param string $inquiry_id
     * @return Inquiry
     * @throws Exceptions\PersonaRecordNotUnique
     * @throws InvalidModelData
     * @throws PersonaRecordNotFound
     */
    public function get(string $inquiry_id): Inquiry
    {
        $response = $this->baseRequest()->get(
            Str::replace(":INQUIRY_ID:", $inquiry_id, ApiEndpoints::INQUIRIES_SINGLE->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return Inquiry::from($response->json());
    }

    /**
     * Permanently deletes personally identifiable information for a given inquiry
     *
     * This action cannot be reverted.
     * This is made to be used to comply with privacy regulations such as GDPR/CCPA or
     * to enforce data privacy
     *
     * @param string $identifier Persona inquiry identifier
     * @return mixed
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    public function redact(string $identifier): Inquiry
    {
        return $this->internalRedact(
            Inquiry::class,
            Str::replace(":INQUIRY_ID:", $identifier, ApiEndpoints::INQUIRIES_SINGLE->value)
        );
    }

    /**
     * Add a new tag to an inquiry
     *
     * @param string $identifier Persona inquiry identifier
     * @param string $new_tag Tag to append, case-insensitive
     * @return Inquiry
     * @throws InvalidModelData|PersonaRecordNotFound
     * @throws InvalidTagName
     */
    public function addTag(string $identifier, string $new_tag): Inquiry
    {
        return $this->internalAddTag(
            Inquiry::class,
            Str::replace(":INQUIRY_ID:", $identifier, ApiEndpoints::INQUIRIES_SINGLE_ADD_TAG->value),
            $new_tag
        );
    }

    /**
     * Remove a tag from an inquiry
     *
     * @param string $identifier Persona inquiry identifier
     * @param string $tag Tag to remove, case-insensitive
     * @return Inquiry
     * @throws InvalidModelData|PersonaRecordNotFound
     * @throws InvalidTagName
     */
    public function removeTag(string $identifier, string $tag): Inquiry
    {
        return $this->internalRemoveTag(
            Inquiry::class,
            Str::replace(":INQUIRY_ID:", $identifier, ApiEndpoints::INQUIRIES_SINGLE_REMOVE_TAG->value),
            $tag
        );
    }

    /**
     * Sync tags to an inquiry
     *
     * @param string $identifier Persona inquiry identifier
     * @param string[] $tags Tag to remove, case-insensitive
     * @return Inquiry
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    public function syncTags(string $identifier, array $tags): Inquiry
    {
        return $this->internalSyncTags(
            Inquiry::class,
            Str::replace(":INQUIRY_ID:", $identifier, ApiEndpoints::INQUIRIES_SINGLE_SYNC_TAGS->value),
            $tags
        );
    }

    /**
     * Retrieve the inquiry pdf.
     *
     * Depending on the requested mode returns the raw binary representation or a file download
     *
     * @param string $inquiry_id Persona inquiry identifier
     * @param RequestMode $mode Operation mode: RAW - returns the binary string; DOWNLOAD - returns a file download
     * @param string $filename Filename used during download, by default inquiry-:INQUIRY_ID:.pdf where :INQUIRY_ID: gets
     *                  substituted by `$inquiry_id`
     * @return string|StreamedResponse
     * @throws PersonaRecordNotFound
     */
    public function getPdf(string $inquiry_id, RequestMode $mode, string $filename = "inquiry-:INQUIRY_ID:.pdf"): string|StreamedResponse
    {
        $response = $this->baseRequest()->get(
            Str::replace(":INQUIRY_ID:", $inquiry_id, ApiEndpoints::INQUIRIES_SINGLE_PDF->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return match ($mode) {
            RequestMode::RAW => $response->body(),
            RequestMode::DOWNLOAD => response()->streamDownload(function () use ($response) {
                echo $response->body();
            }, Str::replace(":INQUIRY_ID:", $inquiry_id, $filename)),
        };
    }

    /**
     * Updates the information linked to an inquiry
     *
     * @param string $inquiry_id
     * @param string $identification_number
     * @param string $locale
     * @param string $selected_country_code
     * @param string $name_first
     * @param string $name_middle
     * @param string $name_last
     * @param Carbon|null $birthdate User's birthdate
     * @param string $email_address User's email address
     * @param string $phone_number User's phone number
     * @param string $address_street_1
     * @param string $address_street_2
     * @param string $address_city User's city of residence address
     * @param string $address_subdivision User's state or subdivision of residence address.
     *                  In the US, this should be the unabbreviated name
     * @param string $address_postal_code ZIP or postal code of residence address
     * @param array $note
     * @return Inquiry
     * @throws Exceptions\PersonaAccountConflict
     * @throws Exceptions\PersonaRecordNotUnique
     * @throws Exceptions\PersonaReferenceCantBeBlank
     * @throws PersonaRecordNotFound
     * @throws InvalidNote
     */
    public function update(
        string  $inquiry_id,
        string  $identification_number = "",
        string  $locale = "",
        string  $selected_country_code = "",
        string  $name_first = "",
        string  $name_middle = "",
        string  $name_last = "",
        ?Carbon $birthdate = null,
        string  $email_address = "",
        string  $phone_number = "",
        string  $address_street_1 = "",
        string  $address_street_2 = "",
        string  $address_city = "",
        string  $address_subdivision = "",
        string  $address_postal_code = "",
        string  $note = ""
    ): Inquiry
    {
        if(strlen($note) > 255) {
            throw new InvalidNote();
        }

        $response = $this->baseRequest()->patch(
            Str::replace(":INQUIRY_ID:", $inquiry_id, ApiEndpoints::INQUIRIES_SINGLE->value),
            [
                "data" => [
                    "attributes" => [
                        "identification_number" => $identification_number,
                        "locale" => $locale,
                        "selected_country_code" => $selected_country_code,
                        "name_first" => $name_first,
                        "name_middle" => $name_middle,
                        "name_last" => $name_last,
                        "birthdate" => $birthdate,
                        "email_address" => $email_address,
                        "phone_number" => $phone_number,
                        "address_street_1" => $address_street_1,
                        "address_street_2" => $address_street_2,
                        "address_city" => $address_city,
                        "address_subdivision" => $address_subdivision,
                        "address_postal_code" => $address_postal_code,
                        "note" => $note,
                    ]
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return Inquiry::from($response->json());
    }

    /**
     * Resume an existing inquiry.
     *
     * When resuming pending inquiries the session token generated here should be provided
     *
     * @param string $inquiry_id Persona inquiry identifier
     * @return Inquiry
     * @throws PersonaRecordNotFound
     */
    public function resume(string $inquiry_id): Inquiry
    {
        $response = $this->baseRequest()->post(
            Str::replace(":INQUIRY_ID:", $inquiry_id, ApiEndpoints::INQUIRIES_SINGLE_RESUME->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return Inquiry::from($response->json());
    }

    /**
     * Approves an existing inquiry.
     *
     * This method triggers workflows and webhooks on Persona's side
     *
     * @param string $inquiry_id Persona inquiry identifier
     * @param string $comment Comment, used for auditing purpose
     * @return Inquiry
     * @throws InvalidModelData
     * @throws PersonaRecordNotFound
     */
    public function approve(string $inquiry_id, string $comment = ""): Inquiry
    {
        $response = $this->baseRequest()->post(
            Str::replace(":INQUIRY_ID:", $inquiry_id, ApiEndpoints::INQUIRIES_SINGLE_APPROVE->value),
            [
                "meta" => [
                    "comment" => $comment
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return Inquiry::from($response->json());
    }

    /**
     * Decline an existing inquiry.
     *
     * This method triggers workflows and webhooks on Persona's side
     *
     * @param string $inquiry_id Persona inquiry identifier
     * @param string $comment Comment, used for auditing purpose
     * @return Inquiry
     * @throws InvalidModelData
     * @throws PersonaRecordNotFound
     */
    public function decline(string $inquiry_id, string $comment = ""): Inquiry
    {
        $response = $this->baseRequest()->post(
            Str::replace(":INQUIRY_ID:", $inquiry_id, ApiEndpoints::INQUIRIES_SINGLE_DECLINE->value),
            [
                "meta" => [
                    "comment" => $comment
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return Inquiry::from($response->json());
    }
}
