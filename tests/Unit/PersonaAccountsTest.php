<?php

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
use Doinc\PersonaKyc\Persona;
use Illuminate\Support\Str;

it('can create account', function () {
    expect(Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX)))->toBeInstanceOf(Account::class);
});

it('cannot create account with extra long reference id', function () {
    expect(Persona::init()->accounts()->create(Str::random(256)))->toBeInstanceOf(Account::class);
})->throws(InvalidReferenceId::class);

it('cannot create account with empty reference id', function () {
    expect(Persona::init()->accounts()->create(""))->toBeInstanceOf(Account::class);
})->throws(PersonaReferenceCantBeBlank::class);

it('cannot create multiple account with same reference id', function () {
    // used to first generate the id, from the second generation the error is thrown
    try {
        Persona::init()->accounts()->create("00");
    } catch (Throwable) {}

    Persona::init()->accounts()->create("00");
})->throws(PersonaAccountConflict::class);

it('can list all accounts', function () {
    expect(Persona::init()->accounts()->list())->toBeInstanceOf(PaginatedAccounts::class);
});

it('can list all accounts after offset', function () {
    $account_list = Persona::init()->accounts()->list();
    $first = $account_list->accounts[0];
    $size = count($account_list->accounts);

    $new_list = Persona::init()->accounts()->list($first->id);
    $new_size = count($new_list->accounts);

    // as if 11 records are present then the record number is always 10 even if starting from position 1
    expect($new_size)->toBeGreaterThanOrEqual($size -1);
    expect($new_list->accounts[0]->id)->not->toEqual($first->id);
});

it('cannot list all accounts after non existing offset', function () {
    Persona::init()->accounts()->list("non-existing");
})->throws(PersonaRecordNotFound::class);

it('can list all accounts before offset', function () {
    Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    $account_list = Persona::init()->accounts()->list($v->id, 2);
    $last = $account_list->accounts[1];

    $new_list = $account_list->previousPage();
    $new_size = count($new_list->accounts);

    expect($new_size)->toEqual(1);
    expect($new_list->accounts[0]->id)->not->toEqual($last->id);
});

it('cannot list all accounts before non existing offset', function () {
    Persona::init()->accounts()->list("non-existing", offset_inverted: true);
})->throws(PersonaRecordNotFound::class);

it('can list all accounts taking 2 by 2', function () {
    $account_list = Persona::init()->accounts()->list(page_size: 2);
    expect(count($account_list->accounts))->toEqual(2);

    $account_list = $account_list->nextPage();
    expect(count($account_list->accounts))->toEqual(2);
});

it('cannot paginate accounts in more than 100', function () {
    Persona::init()->accounts()->list(page_size: 101);
})->throws(InvalidPageSize::class);

it('cannot paginate accounts in less than 1', function () {
    Persona::init()->accounts()->list(page_size: -1);
})->throws(InvalidPageSize::class);

it('can filter accounts', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    Persona::init()->accounts()->create($reference);
    $account_list = Persona::init()->accounts()->list(filter_reference_id: $reference);
    expect(count($account_list->accounts))->toEqual(1);
});

it('can get account by id', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    expect(Persona::init()->accounts()->get($v->id))->toBeInstanceOf(Account::class);
    expect(Persona::init()->accounts()->get($v->id)->id)->toEqual($v->id);
});

it('cannot get account with invalid id', function () {
    Persona::init()->accounts()->get("invalid");
})->throws(PersonaRecordNotFound::class);

it('can redact account by id', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    expect(Persona::init()->accounts()->redact($v->id))->toBeInstanceOf(Account::class);
    expect(Persona::init()->accounts()->redact($v->id)->id)->toEqual($v->id);
});

it('cannot redact account with invalid id', function () {
    Persona::init()->accounts()->redact("invalid");
})->throws(PersonaRecordNotFound::class);

it('can update account by id', function () {
    $reference_id = "" . random_int(0, PHP_INT_MAX);
    $v = Persona::init()->accounts()->create($reference_id);

    $update = Persona::init()->accounts()->update($v->id, $reference_id, first_name: "test", tags: ["a", "b"]);
    expect($update)->toBeInstanceOf(Account::class);
    expect($update->id)->toEqual($v->id);
    expect($update->tags)->toEqual(["A", "B"]);
});

it('cannot update account with invalid id', function () {
    Persona::init()->accounts()->update("invalid", "12345");
})->throws(PersonaRecordNotFound::class);

it('cannot update account with already existing reference id', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->update($v->id, "0");
})->throws(PersonaRecordNotUnique::class);

it('cannot update account with extra long tags', function () {
    $reference = "" . random_int(0, PHP_INT_MAX);
    $v = Persona::init()->accounts()->create($reference);
    Persona::init()->accounts()->update($v->id, $reference, tags: [Str::random(256)]);
})->throws(InvalidTagName::class);

it("cannot update account to extra long phone number", function() {
    $reference = "" . random_int(0, PHP_INT_MAX);
    $v = Persona::init()->accounts()->create($reference);
    Persona::init()->accounts()->update($v->id, $reference, phone_number: Str::random(1635));
})->throws(InvalidPhoneNumber::class);

it("cannot update account with blank reference id", function() {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->update($v->id, "");
})->throws(PersonaReferenceCantBeBlank::class);

it("cannot update account with extra long reference id", function() {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->update($v->id, Str::random(256));
})->throws(InvalidReferenceId::class);

it('can add tag to account', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));

    $new = Persona::init()->accounts()->addTag($v->id, "test-tag");
    expect($new)->toBeInstanceOf(Account::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual(["TEST-TAG"]);
});

it('cannot add tag to non existing account', function () {
    Persona::init()->accounts()->addTag("invalid", "test-tag");
})->throws(PersonaRecordNotFound::class);

it('cannot add extra long tag to account', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->addTag($v->id, Str::random(256));
})->throws(InvalidTagName::class);

it('can remove tag from account', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));

    $new = Persona::init()->accounts()->addTag($v->id, "test-tag");
    expect($new)->toBeInstanceOf(Account::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual(["TEST-TAG"]);

    $new = Persona::init()->accounts()->removeTag($v->id, "test-tag");
    expect($new)->toBeInstanceOf(Account::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual([]);
});

it('cannot remove extra long tag from account', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->removeTag($v->id, Str::random(256));
})->throws(InvalidTagName::class);

it('cannot remove tag from non existing account', function () {
    Persona::init()->accounts()->removeTag("invalid", "test-tag");
})->throws(PersonaRecordNotFound::class);

it('can sync tags of account', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));

    $new = Persona::init()->accounts()->syncTags($v->id, ["test-tag-0", "test-tag-1"]);
    expect($new)->toBeInstanceOf(Account::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual(["TEST-TAG-0", "TEST-TAG-1"]);

    $new = Persona::init()->accounts()->syncTags($v->id, []);
    expect($new)->toBeInstanceOf(Account::class);
    expect($new->id)->toEqual($v->id);
    expect($new->tags)->toEqual([]);
});

it('cannot sync extra long tags of account', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->syncTags($v->id, [Str::random(256)]);
})->throws(InvalidTagName::class);

it('cannot sync tag to non existing account', function () {
    Persona::init()->accounts()->syncTags("invalid", ["test-tag"]);
})->throws(PersonaRecordNotFound::class);

it('can merge accounts', function () {
    $v = Persona::init()->accounts()->create("0-" . random_int(0, PHP_INT_MAX));

    $new = Persona::init()->accounts()->merge($v->id,
        array_map(
            fn(Account $value) => $value->id,
            Persona::init()->accounts()->list($v->id, 100)->accounts
        )
    );
    expect($new)->toBeInstanceOf(Account::class);
    expect($new->id)->toEqual($v->id);
});

it('cannot merge to non existing accounts', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->merge("invalid", [$v->id]);
})->throws(PersonaRecordNotFound::class);

it('cannot merge from non existing accounts', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    Persona::init()->accounts()->merge($v->id, ["invalid"]);
})->throws(PersonaRecordNotFound::class);

it('can self merge', function () {
    $v = Persona::init()->accounts()->create("" . random_int(0, PHP_INT_MAX));
    $new = Persona::init()->accounts()->merge($v->id, [$v->id]);

    expect($new)->toBeInstanceOf(Account::class);
    expect($new->id)->toEqual($v->id);
});
