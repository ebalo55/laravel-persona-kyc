<?php

use Doinc\PersonaKyc\Enums\ApiEndpoints;
use Doinc\PersonaKyc\Enums\EventTypes;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\RequestMode;
use Doinc\PersonaKyc\Exceptions\InvalidNote;
use Doinc\PersonaKyc\Exceptions\InvalidPageSize;
use Doinc\PersonaKyc\Exceptions\InvalidParameter;
use Doinc\PersonaKyc\Exceptions\InvalidTagName;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
use Doinc\PersonaKyc\Exceptions\UnsafeUrl;
use Doinc\PersonaKyc\Models\Account;
use Doinc\PersonaKyc\Models\Document;
use Doinc\PersonaKyc\Models\Event;
use Doinc\PersonaKyc\Models\Inquiry;
use Doinc\PersonaKyc\Models\PaginatedEvents;
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
it('can get event by id', function () {
    $id = env("PERSONA_EVENT_TEST_ID");
    expect(Persona::init()->events()->get($id))->toBeInstanceOf(Event::class);
    expect(Persona::init()->events()->get($id)->id)->toEqual($id);
});

it('cannot get event with invalid id', function () {
    Persona::init()->events()->get("invalid");
})->throws(PersonaRecordNotFound::class);

it('can list all events', function () {
    expect(Persona::init()->events()->list())->toBeInstanceOf(PaginatedEvents::class);
});

it('can list all events after offset', function () {
    $event_list = Persona::init()->events()->list();
    $first = $event_list->events[0];
    $size = count($event_list->events);

    $new_list = Persona::init()->events()->list($first->id);
    $new_size = count($new_list->events);

    // as if 11 records are present then the record number is always 10 even if starting from position 1
    expect($new_size)->toBeGreaterThanOrEqual($size -1);
    expect($new_list->events[0]->id)->not->toEqual($first->id);
});

it('cannot list all events after non existing offset', function () {
    Persona::init()->events()->list("non-existing");
})->throws(PersonaRecordNotFound::class);

it('can list all events before offset', function () {
    $event_list = Persona::init()->events()->list();
    $ev = $event_list->events[4];
    $event_list = Persona::init()->events()->list($ev->id, 2);
    $last = $event_list->events[1];

    $new_list = $event_list->previousPage();
    $new_size = count($new_list->events);

    expect($new_size)->toEqual(2);
    expect($new_list->events[0]->id)->not->toEqual($last->id);
});

it('cannot list all events before non existing offset', function () {
    Persona::init()->events()->list("non-existing", offset_inverted: true);
})->throws(PersonaRecordNotFound::class);

it('can list all events taking 2 by 2', function () {
    $event_list = Persona::init()->events()->list(page_size: 2);
    expect(count($event_list->events))->toEqual(2);

    $event_list = $event_list->nextPage();
    expect(count($event_list->events))->toEqual(2);
});

it('cannot paginate events in more than 100', function () {
    Persona::init()->events()->list(page_size: 101);
})->throws(InvalidPageSize::class);

it('cannot paginate events in less than 1', function () {
    Persona::init()->events()->list(page_size: -1);
})->throws(InvalidPageSize::class);

it('can filter events by name', function () {
    $event_list = Persona::init()->events()->list(filter_event_names: [EventTypes::ACCOUNT_CREATED]);
    expect(count($event_list->events))->toEqual(10);

    foreach ($event_list->events as $ev) {
        expect($ev->payload())->toBeInstanceOf(Account::class);
    }
});

it('can filter events by object id', function () {
    $event_list = Persona::init()->events()->list(filter_event_names: [EventTypes::ACCOUNT_CREATED], page_size: 2);
    expect(count($event_list->events))->toEqual(2);

    $obj_id_array = [];
    foreach ($event_list->events as $ev) {
        expect($ev->payload())->toBeInstanceOf(Account::class);
        $obj_id_array[] = $ev->payload()->id;
    }

    $new = Persona::init()->events()->list(filter_object_id: $obj_id_array);
    foreach ($new->events as $ev) {
        expect($ev->payload())->toBeInstanceOf(Account::class);
        expect(in_array($ev->payload()->id, $obj_id_array))->toBeTrue();
    }
});

it('can filter events by event id', function () {
    $event_list = Persona::init()->events()->list(filter_event_names: [EventTypes::ACCOUNT_CREATED], page_size: 2);
    expect(count($event_list->events))->toEqual(2);

    $ev_id_array = [];
    foreach ($event_list->events as $ev) {
        expect($ev->payload())->toBeInstanceOf(Account::class);
        $ev_id_array[] = $ev->id;
    }

    $new = Persona::init()->events()->list(filter_id: $ev_id_array);
    foreach ($new->events as $ev) {
        expect(in_array($ev->id, $ev_id_array))->toBeTrue();
    }
});
