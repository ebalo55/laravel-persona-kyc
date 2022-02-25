<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidNote extends InvalidParameter
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "note",
            255,
            10012
        );
    }
}
