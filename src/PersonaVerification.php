<?php

namespace Doinc\PersonaKyc;

use Carbon\Carbon;
use Doinc\PersonaKyc\Base\PersonaBaseAccessor;
use Doinc\PersonaKyc\Base\PersonaBaseInitializer;
use Doinc\PersonaKyc\Base\Redactable;
use Doinc\PersonaKyc\Base\Taggable;
use Doinc\PersonaKyc\Enums\ApiEndpoints;
use Doinc\PersonaKyc\Enums\RequestMode;
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
use Doinc\PersonaKyc\Models\Verification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonaVerification extends PersonaBaseAccessor
{
    use PersonaBaseInitializer;

    /**
     * Returns the verification identified by given id
     *
     * @param string $verification_id Persona verification identifier
     * @return Verification
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    public function get(string $verification_id): Verification
    {
        $response = $this->baseRequest()->get(
            Str::replace(":VERIFICATION_ID:", $verification_id, ApiEndpoints::VERIFICATIONS_SINGLE->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return Verification::from($response->json());
    }

    /**
     * Retrieve the verification pdf.
     *
     * Depending on the requested mode returns the raw binary representation or a file download
     *
     * @param string $verification_id Persona verification identifier
     * @param RequestMode $mode Operation mode: RAW - returns the binary string; DOWNLOAD - returns a file download
     * @param string $filename Filename used during download, by default verification-:VERIFICATION_ID:.pdf where :VERIFICATION_ID: gets
     *                  substituted by `$verification_id`
     * @return string|StreamedResponse
     * @throws PersonaRecordNotFound
     */
    public function getPdf(string $verification_id, RequestMode $mode, string $filename = "verification-:VERIFICATION_ID:.pdf"): string|StreamedResponse
    {
        $response = $this->baseRequest()->get(
            Str::replace(":VERIFICATION_ID:", $verification_id, ApiEndpoints::VERIFICATIONS_SINGLE_PDF->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return match ($mode) {
            RequestMode::RAW => $response->body(),
            RequestMode::DOWNLOAD => response()->streamDownload(function () use ($response) {
                echo $response->body();
            }, Str::replace(":VERIFICATION_ID:", $verification_id, $filename)),
        };
    }
}
