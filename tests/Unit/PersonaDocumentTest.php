<?php

use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\RequestMode;
use Doinc\PersonaKyc\Exceptions\InvalidNote;
use Doinc\PersonaKyc\Exceptions\InvalidPageSize;
use Doinc\PersonaKyc\Exceptions\InvalidParameter;
use Doinc\PersonaKyc\Exceptions\InvalidTagName;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
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
it('can get document by id', function () {
    $id = env("PERSONA_DOCUMENT_TEST_ID", null);
    $v = Persona::init()->documents()->get($id);

    expect($v)->toBeInstanceOf(Document::class);
    expect($v->relationships)->not->toBeNull();
    expect($v->relationships->inquiry)->not->toBeNull();
});

it('cannot get non existing document', function () {
    Persona::init()->documents()->get("non-existing");
})->throws(PersonaRecordNotFound::class);
