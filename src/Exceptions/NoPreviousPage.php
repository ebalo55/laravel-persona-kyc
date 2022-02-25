<?php
namespace Doinc\PersonaKyc\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class NoPreviousPage extends Exception
{
    #[Pure]
    public function __construct()
    {
        parent::__construct(
            "No previous page found",
            10003
        );
    }
}
