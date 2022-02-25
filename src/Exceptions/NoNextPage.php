<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class NoNextPage extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "No next page found",
            10004
        );
    }
}
