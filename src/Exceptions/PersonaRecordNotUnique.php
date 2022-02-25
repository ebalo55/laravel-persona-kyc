<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class PersonaRecordNotUnique extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "Record not unique",
            10006
        );
    }
}
