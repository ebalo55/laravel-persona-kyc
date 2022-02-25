<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class PersonaAccountConflict extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "Account already exists with this reference ID",
            10001
        );
    }
}
