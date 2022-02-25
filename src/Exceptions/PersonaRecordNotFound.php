<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class PersonaRecordNotFound extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "Record not found",
            10000
        );
    }
}
