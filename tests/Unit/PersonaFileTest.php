<?php

use Doinc\PersonaKyc\Enums\ApiEndpoints;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\RequestMode;
use Doinc\PersonaKyc\Exceptions\InvalidNote;
use Doinc\PersonaKyc\Exceptions\InvalidPageSize;
use Doinc\PersonaKyc\Exceptions\InvalidParameter;
use Doinc\PersonaKyc\Exceptions\InvalidTagName;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
use Doinc\PersonaKyc\Exceptions\UnsafeUrl;
use Doinc\PersonaKyc\Models\Document;
use Doinc\PersonaKyc\Models\Inquiry;
use Doinc\PersonaKyc\Models\PaginatedInquiries;
use Doinc\PersonaKyc\Models\Verification;
use Doinc\PersonaKyc\Persona;
use Doinc\PersonaKyc\Tests\Assets\PersonaTemplates;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;


/*
|---------------------------------------------------------
| Inquiries
|---------------------------------------------------------
 */
it('can download file by id', function () {
    $org_id = env("PERSONA_ORGANIZATION_TEST_ID", null);
    $filename = env("PERSONA_FILENAME_TEST_ID", null);

    $raw = Persona::init()->files()->download($org_id, $filename, RequestMode::RAW);

    expect(strlen($raw))->toBeGreaterThan(1000);
});

it('can download streamed file by id', function () {
    $org_id = env("PERSONA_ORGANIZATION_TEST_ID", null);
    $filename = env("PERSONA_FILENAME_TEST_ID", null);

    $response = Persona::init()->files()->download($org_id, $filename, RequestMode::DOWNLOAD);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

it('cannot download non owned organization file', function () {
    Persona::init()->files()->download("invalid", "non-existing", RequestMode::RAW);
})->throws(PersonaRecordNotFound::class);

it('can download file via full url', function () {
    $org_id = env("PERSONA_ORGANIZATION_TEST_ID", null);
    $filename = env("PERSONA_FILENAME_TEST_ID", null);
    $url = Str::replace([":ORGANIZATION_ID:", ":FILENAME:"], [$org_id, $filename], ApiEndpoints::FILE_SINGLE->value);

    $raw = Persona::init()->files()->downloadFromUrl($url, RequestMode::RAW);
    expect(strlen($raw))->toBeGreaterThan(1000);
});

it('can download streamed file via full url', function () {
    $org_id = env("PERSONA_ORGANIZATION_TEST_ID", null);
    $filename = env("PERSONA_FILENAME_TEST_ID", null);
    $url = Str::replace([":ORGANIZATION_ID:", ":FILENAME:"], [$org_id, $filename], ApiEndpoints::FILE_SINGLE->value);

    $response = Persona::init()->files()->downloadFromUrl($url, RequestMode::DOWNLOAD);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

it('cannot download from unsafe url', function () {
    Persona::init()->files()->downloadFromUrl("https://www.google.com/", RequestMode::RAW);
})->throws(UnsafeUrl::class);
