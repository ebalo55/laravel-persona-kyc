<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidModelData extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "Invalid model data provided to generator",
            10002
        );
    }
}
