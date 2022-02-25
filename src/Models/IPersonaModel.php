<?php

namespace Doinc\PersonaKyc\Models;

interface IPersonaModel
{
    /**
     * Parse a json array returning a new model instance
     *
     * @param array $array
     * @return static
     */
    public static function from(array $array): self;
}
