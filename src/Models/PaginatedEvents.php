<?php

namespace Doinc\PersonaKyc\Models;

use ArrayAccess;
use Doinc\PersonaKyc\Exceptions\NoNextPage;
use Doinc\PersonaKyc\Exceptions\NoPreviousPage;
use Illuminate\Support\Arr;

class PaginatedEvents extends PersonaPagination implements IPersonaModel
{
    use PersonaModel;

    /** @var Event[]  */
    public readonly array $events;

    private function __construct(array $arr)
    {
        parent::__construct($arr);

        $tmp = [];
        foreach (Arr::get($arr, "data") as $ev) {
            $tmp[] = Event::from(["data" => $ev]);
        }
        $this->events = $tmp;
    }

    protected static function requiredKeys(): array
    {
        return [
            "data",
            "links"
        ];
    }

    public static function from(array $array): PaginatedEvents
    {
        return self::fromExtended($array, null, false);
    }

    /**
     * @return PaginatedEvents
     * @throws NoNextPage
     */
    public function nextPage(): PaginatedEvents
    {
        return parent::nextPage();
    }

    /**
     * @return PaginatedEvents
     * @throws NoPreviousPage
     */
    public function previousPage(): PaginatedEvents
    {
        return parent::previousPage();
    }
}
