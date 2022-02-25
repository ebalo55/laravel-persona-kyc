<?php

namespace Doinc\PersonaKyc;

use Carbon\Carbon;
use Doinc\PersonaKyc\Base\PersonaBaseAccessor;
use Doinc\PersonaKyc\Base\PersonaBaseInitializer;
use Doinc\PersonaKyc\Base\Redactable;
use Doinc\PersonaKyc\Base\Taggable;
use Doinc\PersonaKyc\Enums\ApiEndpoints;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Exceptions\InvalidPageSize;
use Doinc\PersonaKyc\Exceptions\InvalidPhoneNumber;
use Doinc\PersonaKyc\Exceptions\InvalidReferenceId;
use Doinc\PersonaKyc\Exceptions\InvalidTagName;
use Doinc\PersonaKyc\Exceptions\PersonaAccountConflict;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotUnique;
use Doinc\PersonaKyc\Exceptions\PersonaReferenceCantBeBlank;
use Doinc\PersonaKyc\Models\Account;
use Doinc\PersonaKyc\Models\PaginatedAccounts;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PersonaAccounts extends PersonaBaseAccessor
{
    use PersonaBaseInitializer, Taggable, Redactable;

    /**
     * Creates a new account to be used in inquiries.
     *
     * @param string $reference_id Identifier used by Persona to group all related user's requests and information.
     *                  This should be the unique identifier of the user for easier usage.
     * @return Account
     * @throws PersonaAccountConflict|InvalidModelData|PersonaReferenceCantBeBlank
     * @throws InvalidReferenceId
     */
    public function create(string $reference_id): Account
    {
        if (empty($reference_id)) {
            throw new PersonaReferenceCantBeBlank();
        }
        if (strlen($reference_id) > 255) {
            throw new InvalidReferenceId();
        }

        $response = $this->baseRequest()->post(ApiEndpoints::ACCOUNTS->value, [
            "data" => [
                "type" => "account",
                "attributes" => [
                    "reference-id" => $reference_id
                ]
            ]
        ]);

        PersonaErrorChecker::checkErrors($response);
        return Account::from($response->json());
    }

    /**
     * Returns a list of accounts
     *
     * @param string $offset Account id from where to start extracting data
     * @param int $page_size Number of records to return per page
     * @param string $filter_reference_id Filter by the provided reference id
     * @param bool $offset_inverted Whether the offset is used to get the previous page or the next one
     * @return PaginatedAccounts
     * @throws PersonaAccountConflict
     * @throws PersonaRecordNotFound
     * @throws InvalidPageSize
     */
    public function list(
        string $offset = "",
        int    $page_size = 10,
        string $filter_reference_id = "",
        bool   $offset_inverted = false
    ): PaginatedAccounts
    {
        if ($page_size < 1 || $page_size > 100) {
            throw new InvalidPageSize();
        }

        $response = $this->baseRequest()->get(ApiEndpoints::ACCOUNTS->value, [
            "page" => [
                "before" => $offset_inverted ? $offset : "",
                "after" => !$offset_inverted ? $offset : "",
                "size" => "$page_size"
            ],
            "filter" => [
                "reference-id" => $filter_reference_id
            ]
        ]);

        PersonaErrorChecker::checkErrors($response);
        return PaginatedAccounts::from($response->json());
    }

    /**
     * Returns the account identified by given id
     *
     * @param string $account_id Persona account identifier
     * @return Account
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    public function get(string $account_id): Account
    {
        $response = $this->baseRequest()->get(
            Str::replace(":ACCOUNT_ID:", $account_id, ApiEndpoints::ACCOUNTS_SINGLE->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return Account::from($response->json());
    }

    /**
     * Permanently deletes personally identifiable information for a given account
     *
     * This action cannot be reverted.
     * This is made to be used to comply with privacy regulations such as GDPR/CCPA or
     * to enforce data privacy
     *
     * NOTE: Account still exists and is still updatable after redaction
     *
     * @param string $identifier Persona account identifier
     * @return Account
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    public function redact(string $identifier): Account
    {
        return $this->internalRedact(
            Account::class,
            Str::replace(":ACCOUNT_ID:", $identifier, ApiEndpoints::ACCOUNTS_SINGLE->value)
        );
    }

    /**
     * Updates the information linked to an account
     *
     * NOTE: Tags gets converted to uppercase
     *
     * @param string $account_id Persona account identifier
     * @param string $reference_id Reference id of the account, if no changes are required pass the previous value
     * @param string $first_name User's first name
     * @param string $middle_name User's middle name
     * @param string $last_name User's last name
     * @param Carbon|null $birthdate User's birthdate
     * @param string $street_address_1 User's street name of residence
     * @param string $street_address_2 User's extension of residence address, usually apartment or suite number
     * @param string $address_city User's city of residence address
     * @param string $address_subdivision User's state or subdivision of residence address.
     *                  In the US, this should be the unabbreviated name
     * @param string $address_postal_code ZIP or postal code of residence address
     * @param string $country_code ISO 3166-1 alpha 2 country code of the government ID to be verified
     * @param string $email_address User's email address
     * @param string $phone_number User's phone number
     * @param string $social_security_number User's social security number
     * @param string[] $tags A list of tag names associated with the account
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotUnique|PersonaReferenceCantBeBlank|InvalidReferenceId
     * @throws InvalidTagName|InvalidPhoneNumber
     */
    public function update(
        string  $account_id,
        string  $reference_id,
        string  $first_name = "",
        string  $middle_name = "",
        string  $last_name = "",
        ?Carbon $birthdate = null,
        string  $street_address_1 = "",
        string  $street_address_2 = "",
        string  $address_city = "",
        string  $address_subdivision = "",
        string  $address_postal_code = "",
        string  $country_code = "",
        string  $email_address = "",
        string  $phone_number = "",
        string  $social_security_number = "",
        array   $tags = []
    ): Account
    {
        if (strlen($reference_id) === 0) {
            throw new PersonaReferenceCantBeBlank();
        }

        if (strlen($reference_id) > 255) {
            throw new InvalidReferenceId();
        }

        if (strlen($phone_number) > 1634) {
            throw new InvalidPhoneNumber();
        }

        $this->checkTags($tags);

        $response = $this->baseRequest()->patch(
            Str::replace(":ACCOUNT_ID:", $account_id, ApiEndpoints::ACCOUNTS_SINGLE->value),
            [
                "data" => [
                    "attributes" => [
                        "tags" => $tags,
                        "reference-id" => $reference_id,
                        "name-first" => $first_name,
                        "name-middle" => $middle_name,
                        "name-last" => $last_name,
                        "birthdate" => !is_null($birthdate) ? $birthdate->toDateString() : "",
                        "address-street-1" => $street_address_1,
                        "address-street-2" => $street_address_2,
                        "address-city" => $address_city,
                        "address-subdivision" => $address_subdivision,
                        "address-postal-code" => $address_postal_code,
                        "country-code" => $country_code,
                        "email-address" => $email_address,
                        "phone-number" => $phone_number,
                        "social-security-number" => $social_security_number,
                    ]
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return Account::from($response->json());
    }

    /**
     * Add a new tag to an account
     *
     * @param string $identifier Persona account identifier
     * @param string $new_tag Tag to append, case-insensitive
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotFound
     * @throws InvalidTagName
     */
    public function addTag(string $identifier, string $new_tag): Account
    {
        return $this->internalAddTag(
            Account::class,
            Str::replace(":ACCOUNT_ID:", $identifier, ApiEndpoints::ACCOUNTS_SINGLE_ADD_TAG->value),
            $new_tag
        );
    }

    /**
     * Remove a tag from an account
     *
     * @param string $identifier Persona account identifier
     * @param string $tag Tag to remove, case-insensitive
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotFound
     * @throws InvalidTagName
     */
    public function removeTag(string $identifier, string $tag): Account
    {
        return $this->internalRemoveTag(
            Account::class,
            Str::replace(":ACCOUNT_ID:", $identifier, ApiEndpoints::ACCOUNTS_SINGLE_REMOVE_TAG->value),
            $tag
        );
    }

    /**
     * Sync tags to from an account
     *
     * @param string $identifier Persona account identifier
     * @param string[] $tags Tag to remove, case-insensitive
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotFound
     */
    public function syncTags(string $identifier, array $tags): Account
    {
        return $this->internalSyncTags(
            Account::class,
            Str::replace(":ACCOUNT_ID:", $identifier, ApiEndpoints::ACCOUNTS_SINGLE_SYNC_TAGS->value),
            $tags
        );
    }

    /**
     * Merges several source Accounts' information into one target Account.
     * Any Inquiry, Verification, Report and Document associated with the source Account will be
     * transferred over to the destination Account.
     * However, the Account's attributes will not be transferred.
     *
     * NOTE: This endpoint can be used to clean up duplicate Accounts.
     * NOTE: A source account can only be consolidated once. Afterwards, the source account will be archived.
     *
     * @param string $destination_account_id
     * @param string[] $source_accounts
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotFound
     */
    public function merge(string $destination_account_id, array $source_accounts): Account
    {
        $response = $this->baseRequest()->post(
            Str::replace(":ACCOUNT_ID:", $destination_account_id, ApiEndpoints::ACCOUNTS_SINGLE_MERGE->value),
            [
                "meta" => [
                    "source-account-ids" => $source_accounts
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return Account::from($response->json());
    }
}
