<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidPageSize extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "Invalid page size provided, value must be between 1 and 100",
            10005
        );
    }
}
