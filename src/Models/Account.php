<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class Account implements IPersonaModel
{
    use PersonaModel;

    public readonly string $id;
    public readonly string $reference_id;
    public readonly Carbon $created_at;
    public readonly Carbon $updated_at;
    public readonly array $tags;

    private function __construct(array $arr)
    {
        $this->id = Arr::get($arr, "data.id");
        $this->reference_id = Arr::get($arr, "data.attributes.reference_id");
        $this->created_at = Carbon::parse(Arr::get($arr, "data.attributes.created_at"));
        $this->updated_at = Carbon::parse(Arr::get($arr, "data.attributes.updated_at"));
        $this->tags = Arr::get($arr, "data.attributes.tags");
    }

    private static function requiredKeys(): array
    {
        return [
            "data.type",
            "data.id",
            "data.attributes",
            "data.attributes.reference_id",
            "data.attributes.created_at",
            "data.attributes.updated_at",
            "data.attributes.tags",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): Account
    {
        return self::fromExtended($array, "account");
    }
}
