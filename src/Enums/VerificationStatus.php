<?php

namespace Doinc\PersonaKyc\Enums;

enum VerificationStatus: string
{
    /**
     * When the individual first starts the verification, the verification is initiated.
     */
    case INITIATED = "initiated";
    /**
     * When the individual submits their information, the server verifies their information.
     */
    case SUBMITTED = "submitted";
    /**
     * Once the server has verified the individual's information, the verification passes.
     */
    case PASSED = "passed";
    /**
     * If the server fails to verify the individual's information, the verification fails.
     */
    case FAILED = "failed";
    /**
     * The individual has verified that they have the physical device by entering a confirmation code.
     * They have not submitted their information yet.
     */
    case CONFIRMED = "confirmed";
}
