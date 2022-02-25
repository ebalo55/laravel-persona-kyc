<?php

use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\RequestMode;
use Doinc\PersonaKyc\Exceptions\InvalidNote;
use Doinc\PersonaKyc\Exceptions\InvalidPageSize;
use Doinc\PersonaKyc\Exceptions\InvalidParameter;
use Doinc\PersonaKyc\Exceptions\InvalidTagName;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
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
it('can get verification by id', function () {
    $id = env("PERSONA_VERIFICATION_TEST_ID", null);
    $v = Persona::init()->verifications()->get($id);

    expect($v)->toBeInstanceOf(Verification::class);
    expect($v->checks)->not->toBeNull();
    expect($v->checks[0]->status)->not->toBeNull();
    expect($v->relationships)->not->toBeNull();
    expect($v->relationships->document)->not->toBeNull();
});

it('cannot get non existing verification', function () {
    Persona::init()->verifications()->get("non-existing");
})->throws(PersonaRecordNotFound::class);

it('can download raw verification', function () {
    $id = env("PERSONA_VERIFICATION_TEST_ID", null);
    $raw = Persona::init()->verifications()->getPdf($id, RequestMode::RAW);

    expect(strlen($raw))->toBeGreaterThan(1000);
});

it('can download streamed verification', function () {
    $id = env("PERSONA_VERIFICATION_TEST_ID", null);
    $response = Persona::init()->verifications()->getPdf($id, RequestMode::DOWNLOAD);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

it('cannot download non existing verification', function () {
    Persona::init()->inquiries()->getPdf("non-existing", RequestMode::RAW);
})->throws(PersonaRecordNotFound::class);
