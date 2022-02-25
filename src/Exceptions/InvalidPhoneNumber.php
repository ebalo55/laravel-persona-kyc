<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidPhoneNumber extends InvalidParameter
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "phone number",
            1634,
            10010
        );
    }
}
