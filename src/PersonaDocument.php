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
use Doinc\PersonaKyc\Models\Document;
use Doinc\PersonaKyc\Models\PaginatedAccounts;
use Doinc\PersonaKyc\Models\Verification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonaDocument extends PersonaBaseAccessor
{
    use PersonaBaseInitializer;

    /**
     * Returns the document identified by given id
     *
     * @param string $document_id Persona document identifier
     * @return Document
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    public function get(string $document_id): Document
    {
        $response = $this->baseRequest()->get(
            Str::replace(":DOCUMENT_ID:", $document_id, ApiEndpoints::DOCUMENT_SINGLE->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return Document::from($response->json());
    }
}
