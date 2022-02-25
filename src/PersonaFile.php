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
use Doinc\PersonaKyc\Exceptions\UnsafeUrl;
use Doinc\PersonaKyc\Models\Account;
use Doinc\PersonaKyc\Models\Document;
use Doinc\PersonaKyc\Models\PaginatedAccounts;
use Doinc\PersonaKyc\Models\Verification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonaFile extends PersonaBaseAccessor
{
    use PersonaBaseInitializer;

    /**
     * Retrieve the file pdf identified by the company id and its filename
     *
     * Depending on the requested mode returns the raw binary representation or a file download
     *
     * @param string $file_id Persona file identifier
     * @param string $filename Filename used during download
     * @param RequestMode $mode Operation mode: RAW - returns the binary string; DOWNLOAD - returns a file download
     * @return string|StreamedResponse
     * @throws PersonaRecordNotFound
     */
    public function download(string $file_id, string $filename, RequestMode $mode): string|StreamedResponse
    {
        $response = $this->baseRequest()->get(
            Str::replace([":ORGANIZATION_ID:", ":FILENAME:"], [$file_id, $filename], ApiEndpoints::FILE_SINGLE->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return match ($mode) {
            RequestMode::RAW => $response->body(),
            RequestMode::DOWNLOAD => response()->streamDownload(function () use ($response) {
                echo $response->body();
            }, $filename),
        };
    }

    /**
     * Retrieve the file pdf identified by the company id and its filename
     *
     * Depending on the requested mode returns the raw binary representation or a file download
     *
     * ALERT: Abusing this endpoint to request external non-Persona endpoints will leak your bearer token,
     *      always check the endpoint you're requesting and avoid any unsafe domain
     *
     * @param string $full_url Persona file full url, usually returned by previous requests
     * @param RequestMode $mode Operation mode: RAW - returns the binary string; DOWNLOAD - returns a file download
     * @return string|StreamedResponse
     * @throws PersonaRecordNotFound
     * @throws UnsafeUrl
     */
    public function downloadFromUrl(string $full_url, RequestMode $mode): string|StreamedResponse
    {
        $regex = Regex::match(
            "/https:\/\/withpersona\.com\/api\/v1\/files\/([a-zA-Z0-9%\-=]+)\/(.+)/",
            $full_url
        );

        if (
            $regex->hasMatch() &&
            Str::startsWith($regex->group(0), "https://withpersona.com/api/v1/files/") &&
            count($regex->groups()) === 3
        ) {
            return $this->download($regex->group(1), $regex->group(2), $mode);
        }
        throw new UnsafeUrl();
    }
}
