<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class VerificationCheck implements IPersonaModel
{
    use PersonaModel;

    public readonly string $name;
    public readonly string $status;
    /**
     * @var string[]
     */
    public readonly array $reasons;
    /**
     * @var Metadata[]
     */
    public readonly array $metadata;

    private function __construct(array $arr)
    {
        $this->name = Arr::get($arr, "name");
        $this->status = Arr::get($arr, "status");
        $this->reasons = Arr::get($arr, "reasons");
        $this->metadata = Arr::get($arr, "metadata");
    }

    private static function requiredKeys(): array
    {
        return [
            "name",
            "status",
            "reasons",
            "metadata",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): VerificationCheck
    {
        return self::fromExtended($array, null, false);
    }
}
