<?php

namespace Doinc\PersonaKyc\Models;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Exceptions\NoNextPage;
use Doinc\PersonaKyc\Exceptions\NoPreviousPage;
use Doinc\PersonaKyc\Persona;
use Illuminate\Support\Arr;
use Spatie\Regex\Regex;
use Throwable;

class PersonaPagination
{
    private ?string $prev_id;
    private ?string $next_id;
    private ?int $per_page;
    private ?string $method;

    protected function __construct(array $arr)
    {
        $required_keys = [
            "links",
            "links.prev",
            "links.next",
        ];

        if (Arr::isAssoc($arr) &&
            Arr::has($arr, $required_keys)
        ) {
            $previous_page = Arr::get($arr, "links.prev");
            $next_page = Arr::get($arr, "links.next");
            $this->prev_id = null;
            $this->next_id = null;
            $this->per_page = null;
            $this->method = null;

            if(!is_null($previous_page)) {
                $this->prev_id = Regex::match("/page%5Bbefore%5D=(\w+)/", $previous_page)->group(1);
            }

            if(!is_null($next_page)) {
                $this->next_id = Regex::match("/page%5Bafter%5D=(\w+)/", $next_page)->group(1);
            }

            try {
                $this->per_page = (int)Regex::match("/page%5Bsize%5D=(\d+)/", $previous_page ?? $next_page)->group(1);
                $this->method = Regex::match("/^\/api\/v1\/(\w+)\?/", $previous_page ?? $next_page)->group(1);
            } catch (Throwable) {}
        }
        else {
            throw new InvalidModelData();
        }
    }

    /**
     * Retrieve the previous page accounts
     *
     * @throws NoPreviousPage
     */
    protected function previousPage(): mixed {
        if(!is_null($this->prev_id)) {
            return Persona::init()->{$this->method}()->list(
                $this->prev_id,
                $this->per_page ?? 10,
                offset_inverted: true
            );
        }
        throw new NoPreviousPage();
    }

    /**
     * @throws NoNextPage
     */
    protected function nextPage(): mixed
    {
        if(!is_null($this->next_id)) {
            return Persona::init()->{$this->method}()->list(
                $this->next_id,
                $this->per_page ?? 10
            );
        }
        throw new NoNextPage();
    }
}
