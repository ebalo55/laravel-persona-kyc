<?php

namespace Doinc\PersonaKyc\Models\RelationMiniModel;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

trait PersonaMiniModel
{
    public readonly string $id;

    /**
     * Private constructor responsible for generating the model instance and parsing the
     * returned values
     *
     * @param array $arr
     */
    protected function __construct(array $arr) {
        $this->id = Arr::get($arr, "data.id");
    }

    /**
     * Array of required keys for the parser to pass
     *
     * @return array
     */
    protected static function requiredKeys(): array {
        return [
            "data.type",
            "data.id",
        ];
    }

    /**
     * Parse a json array returning a new model instance
     *
     * @param array $array
     * @param string|null $type
     * @param bool $type_check
     * @return static
     * @throws InvalidModelData
     */
    protected static function fromExtended(array $array, ?string $type, bool $type_check = true): static
    {
        if (Arr::isAssoc($array) &&
            Arr::has($array, self::requiredKeys()) &&
            (
                !$type_check ||
                Arr::get($array, "data.type") === $type
            )
        ) {
            return new static($array);
        }
        throw new InvalidModelData();
    }
}
