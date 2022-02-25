<?php

namespace Doinc\PersonaKyc;

use Doinc\PersonaKyc\Exceptions\InvalidParameter;
use Doinc\PersonaKyc\Exceptions\PersonaAccountConflict;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotUnique;
use Doinc\PersonaKyc\Exceptions\PersonaReferenceCantBeBlank;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class PersonaErrorChecker
{
    /**
     * Check for errors in response and throw them.
     *
     * @throws PersonaRecordNotFound
     * @throws PersonaAccountConflict
     * @throws PersonaRecordNotUnique
     * @throws PersonaReferenceCantBeBlank
     * @throws InvalidParameter
     */
    public static function checkErrors(Response $response) {
        $json = collect($response->json());
        if($json->has("errors")) {
            foreach ($json->get("errors") as $error) {
                if($error["title"] === "Record not found") {
                    throw new PersonaRecordNotFound();
                }
                elseif (
                    $error["title"] === "Conflict" &&
                    Arr::has($error, "details") &&
                    $error["details"] === "Account already exists with this reference ID"
                ) {
                    throw new PersonaAccountConflict();
                }
                elseif ($error["title"] === "Record not unique") {
                    throw new PersonaRecordNotUnique();
                }
                elseif (
                    $error["title"] === "Bad request" &&
                    Arr::has($error, "details") &&
                    $error["details"] === "Reference can't be blank"
                ) {
                    throw new PersonaReferenceCantBeBlank();
                }
                elseif (
                    $error["title"] === "Bad request" &&
                    Arr::has($error, "details") &&
                    $error["details"] === "must be shorter than 255 characters"
                ) {
                    throw new InvalidParameter("parameter", 255, 10011);
                }
            }
        }
    }
}
