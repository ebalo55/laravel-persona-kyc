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
it('creates a new inquiry', function () {
    $reference_id = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference_id);
    expect(Persona::init()->inquiries()->create($reference_id, PersonaTemplates::GOVERNMENT_ID))
        ->toBeInstanceOf(Inquiry::class);
});

it('cannot create inquiry with invalid reference id', function () {
    Persona::init()->inquiries()->create("-1", PersonaTemplates::GOVERNMENT_ID_AND_SELFIE);
})->throws(PersonaRecordNotFound::class);

it('throws if invalid template is provided', function () {
    Persona::init()->inquiries()->create("0", PersonaTemplates::GOVERNMENT_ID_AND_SELFIE);
})->throws(PersonaRecordNotFound::class);

it('can list all inquiries', function () {
    expect(Persona::init()->inquiries()->list())->toBeInstanceOf(PaginatedInquiries::class);
});

it('can list all inquiries after offset', function () {
    $inquiry_list = Persona::init()->inquiries()->list();
    $first = $inquiry_list->inquiries[0];
    $size = count($inquiry_list->inquiries);

    $new_list = Persona::init()->inquiries()->list($first->id);
    $new_size = count($new_list->inquiries);

    // as if 11 records are present then the record number is always 10 even if starting from position 1
    expect($new_size)->toBeGreaterThanOrEqual($size -1);
    expect($new_list->inquiries[0]->id)->not->toEqual($first->id);
});

it('cannot list all inquiries after non existing offset', function () {
    Persona::init()->inquiries()->list("non-existing");
})->throws(PersonaRecordNotFound::class);

it('can list all inquiries before offset', function () {
    Persona::init()->inquiries()->create("" . random_int(0, PHP_INT_MAX), PersonaTemplates::GOVERNMENT_ID);
    $v = Persona::init()->inquiries()->create("" . random_int(0, PHP_INT_MAX), PersonaTemplates::GOVERNMENT_ID);
    $inquiry_list = Persona::init()->inquiries()->list($v->id, 2);
    $last = $inquiry_list->inquiries[1];

    $new_list = $inquiry_list->previousPage();
    $new_size = count($new_list->inquiries);

    expect($new_size)->toEqual(1);
    expect($new_list->inquiries[0]->id)->not->toEqual($last->id);
});

it('cannot list all inquiries before non existing offset', function () {
    Persona::init()->inquiries()->list("non-existing", offset_inverted: true);
})->throws(PersonaRecordNotFound::class);

it('can list all inquiries taking 2 by 2', function () {
    $inquiry_list = Persona::init()->inquiries()->list(page_size: 2);
    expect(count($inquiry_list->inquiries))->toEqual(2);

    $inquiry_list = $inquiry_list->nextPage();
    expect(count($inquiry_list->inquiries))->toEqual(2);
});

it('cannot paginate inquiries in more than 100', function () {
    Persona::init()->inquiries()->list(page_size: 101);
})->throws(InvalidPageSize::class);

it('cannot paginate inquiries in less than 1', function () {
    Persona::init()->inquiries()->list(page_size: -1);
})->throws(InvalidPageSize::class);

it('can filter inquiries by reference id', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $inquiry_list = Persona::init()->inquiries()->list(filter_reference_id: $reference);
    expect(count($inquiry_list->inquiries))->toEqual(1);
});

it('can filter inquiries by inquiry id', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    $account = Persona::init()->accounts()->create($reference);
    Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $inquiry_list = Persona::init()->inquiries()->list(filter_account_id: $account->id);
    expect(count($inquiry_list->inquiries))->toEqual(1);
});

it('can get inquiry by id', function () {
    $v = Persona::init()->inquiries()->create("" . random_int(0, PHP_INT_MAX), PersonaTemplates::GOVERNMENT_ID);
    expect(Persona::init()->inquiries()->get($v->id))->toBeInstanceOf(Inquiry::class);
    expect(Persona::init()->inquiries()->get($v->id)->id)->toEqual($v->id);
});

it('cannot get inquiry with invalid id', function () {
    Persona::init()->inquiries()->get("invalid");
})->throws(PersonaRecordNotFound::class);

it('can redact inquiry by id', function () {
    $v = Persona::init()->inquiries()->create("" . random_int(0, PHP_INT_MAX), PersonaTemplates::GOVERNMENT_ID);
    expect(Persona::init()->inquiries()->redact($v->id))->toBeInstanceOf(Inquiry::class);
    expect(Persona::init()->inquiries()->redact($v->id)->id)->toEqual($v->id);
});

it('cannot redact inquiry with invalid id', function () {
    Persona::init()->inquiries()->redact("invalid");
})->throws(PersonaRecordNotFound::class);

it('can add tag to inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);

    $new = Persona::init()->inquiries()->addTag($v->id, "test-tag");
    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual(["TEST-TAG"]);
});

it('cannot add tag to non existing inquiry', function () {
    Persona::init()->inquiries()->addTag("invalid", "test-tag");
})->throws(PersonaRecordNotFound::class);

it('cannot add extra long tag to inquiry', function () {
    $v = Persona::init()->inquiries()->create("" . random_int(0, PHP_INT_MAX), PersonaTemplates::GOVERNMENT_ID);
    Persona::init()->inquiries()->addTag($v->id, Str::random(256));
})->throws(InvalidTagName::class);

it('can remove tag from inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);

    $new = Persona::init()->inquiries()->addTag($v->id, "test-tag");
    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual(["TEST-TAG"]);

    $new = Persona::init()->inquiries()->removeTag($v->id, "test-tag");
    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual([]);
});

it('cannot remove extra long tag from inquiry', function () {
    $v = Persona::init()->inquiries()->create("" . random_int(0, PHP_INT_MAX), PersonaTemplates::GOVERNMENT_ID);
    Persona::init()->inquiries()->removeTag($v->id, Str::random(256));
})->throws(InvalidTagName::class);

it('cannot remove tag from non existing inquiry', function () {
    Persona::init()->inquiries()->removeTag("invalid", "test-tag");
})->throws(PersonaRecordNotFound::class);

it('can sync tags of inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);

    $new = Persona::init()->inquiries()->syncTags($v->id, ["test-tag-0", "test-tag-1"]);
    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual(["TEST-TAG-0", "TEST-TAG-1"]);

    $new = Persona::init()->inquiries()->syncTags($v->id, []);
    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual([]);
});

it('cannot sync extra long tags of inquiry', function () {
    $v = Persona::init()->inquiries()->create("" . random_int(0, PHP_INT_MAX), PersonaTemplates::GOVERNMENT_ID);
    Persona::init()->inquiries()->syncTags($v->id, [Str::random(256)]);
})->throws(InvalidTagName::class);

it('cannot sync tag to non existing inquiry', function () {
    Persona::init()->inquiries()->syncTags("invalid", ["test-tag"]);
})->throws(PersonaRecordNotFound::class);

it('can download raw inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $raw = Persona::init()->inquiries()->getPdf($v->id, RequestMode::RAW);

    expect(strlen($raw))->toBeGreaterThan(1000);
});

it('can download streamed inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $response = Persona::init()->inquiries()->getPdf($v->id, RequestMode::DOWNLOAD);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

it('cannot download non existing inquiry', function () {
    Persona::init()->inquiries()->getPdf("non-existing", RequestMode::RAW);
})->throws(PersonaRecordNotFound::class);

it('can update inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id);

    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
});

it('cannot update inquiry with extra long security number', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long country code', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long first name', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long middle name', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long last name', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long email', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long phone number', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long street address 1', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long street address 2', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), "", "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long city', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), "", "", "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long address subdivision', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), "", "", "", "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long address zip code', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), "", "", "", "", "", "", Str::random(256));
})->throws(InvalidParameter::class);

it('cannot update inquiry with extra long note', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->update($v->id, "", "", "", "", "", "", now(), "", "", "", "", "", "", "", Str::random(256));
})->throws(InvalidNote::class);

it('can resume inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->resume($v->id);

    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->metadata)->not->toBeNull();
    expect($new->metadata->session_token)->not->toBeNull();
    expect($new->relationships->sessions)->not->toBeNull();
    expect($new->relationships->sessions[0]->id)->not->toBeNull();
});

it('cannot resume non existing inquiry', function () {
    Persona::init()->inquiries()->resume("non-existing");
})->throws(PersonaRecordNotFound::class);

it('can approve inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->approve($v->id);

    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->status)->toEqual(InquiryStatus::APPROVED);
});

it('can approve inquiry with extra long comment', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->approve($v->id, Str::random(10000));

    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->status)->toEqual(InquiryStatus::APPROVED);
});

it('cannot approve non existing inquiry', function () {
    Persona::init()->inquiries()->approve("non-existing");
})->throws(PersonaRecordNotFound::class);

it('can decline inquiry', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->decline($v->id);

    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->status)->toEqual(InquiryStatus::DECLINED);
});

it('can decline inquiry with extra long comment', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $v = Persona::init()->inquiries()->create($reference, PersonaTemplates::GOVERNMENT_ID);
    $new = Persona::init()->inquiries()->decline($v->id, Str::random(10000));

    expect($new)->toBeInstanceOf(Inquiry::class);
    expect($new->id)->toEqual($v->id);
    expect($new->status)->toEqual(InquiryStatus::DECLINED);
});

it('cannot decline non existing inquiry', function () {
    Persona::init()->inquiries()->decline("non-existing");
})->throws(PersonaRecordNotFound::class);
