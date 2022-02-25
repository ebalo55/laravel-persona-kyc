<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidReferenceId extends InvalidParameter
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "reference id",
            255,
            10008
        );
    }
}
