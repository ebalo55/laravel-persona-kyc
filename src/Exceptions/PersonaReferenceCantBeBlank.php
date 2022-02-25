<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class PersonaReferenceCantBeBlank extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "Invalid reference id provided, reference can't be blank",
            10007
        );
    }
}
