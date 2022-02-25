<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidParameter extends Exception
{
    #[Pure]
    public function __construct(string $parameter_name, int $max_length, int $code)
    {
        parent::__construct(
            "Invalid $parameter_name provided, its length must be within 1 and $max_length characters",
            $code
        );
    }
}
