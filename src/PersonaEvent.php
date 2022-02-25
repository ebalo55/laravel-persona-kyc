<?php

namespace Doinc\PersonaKyc;

use Carbon\Carbon;
use Doinc\PersonaKyc\Base\PersonaBaseAccessor;
use Doinc\PersonaKyc\Base\PersonaBaseInitializer;
use Doinc\PersonaKyc\Base\Redactable;
use Doinc\PersonaKyc\Base\Taggable;
use Doinc\PersonaKyc\Enums\ApiEndpoints;
use Doinc\PersonaKyc\Enums\EventTypes;
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
use Doinc\PersonaKyc\Models\Event;
use Doinc\PersonaKyc\Models\PaginatedAccounts;
use Doinc\PersonaKyc\Models\PaginatedEvents;
use Doinc\PersonaKyc\Models\Verification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonaEvent extends PersonaBaseAccessor
{
    use PersonaBaseInitializer;

    /**
     * Returns the event identified by given id
     *
     * @param string $event_id Persona event identifier
     * @return Event
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    public function get(string $event_id): Event
    {
        $response = $this->baseRequest()->get(
            Str::replace(":EVENT_ID:", $event_id, ApiEndpoints::EVENTS_SINGLE->value)
        );

        PersonaErrorChecker::checkErrors($response);
        return Event::from($response->json());
    }

    /**
     * Returns a list of events
     *
     * @param string $offset Event id from where to start extracting data
     * @param int $page_size Number of records to return per page
     * @param EventTypes[] $filter_event_names Filter by the provided list of event names
     * @param string[] $filter_object_id Filter by the provided list of object id
     * @param string[] $filter_id Filter by the provided list of event id
     * @param bool $offset_inverted Whether the offset is used to get the previous page or the next one
     * @return PaginatedEvents
     * @throws PersonaAccountConflict
     * @throws PersonaRecordNotFound
     * @throws InvalidPageSize
     */
    public function list(
        string $offset = "",
        int    $page_size = 10,
        array  $filter_event_names = [],
        array  $filter_object_id = [],
        array  $filter_id = [],
        bool   $offset_inverted = false
    ): PaginatedEvents
    {
        if ($page_size < 1 || $page_size > 100) {
            throw new InvalidPageSize();
        }

        $response = $this->baseRequest()->get(ApiEndpoints::EVENTS->value, [
            "page" => [
                "before" => $offset_inverted ? $offset : "",
                "after" => !$offset_inverted ? $offset : "",
                "size" => "$page_size"
            ],
            "filter" => [
                "name" => count($filter_event_names) > 0 ?
                    implode(
                        ",",
                        array_map(
                            function(EventTypes $v) {
                                return $v->value;
                            },
                            $filter_event_names
                        )
                    ) : "",
                "object_id" => count($filter_object_id) > 0 ? implode(",", $filter_object_id) : "",
                "id" => count($filter_id) > 0 ? implode(",", $filter_id) : "",
            ]
        ]);

        PersonaErrorChecker::checkErrors($response);
        return PaginatedEvents::from($response->json());
    }
}
