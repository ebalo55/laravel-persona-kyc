<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class UnsafeUrl extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "Unsafe url detected",
            10013
        );
    }
}
