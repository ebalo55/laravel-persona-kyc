<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidTagName extends InvalidParameter
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "tag name",
            255,
            10009
        );
    }
}
