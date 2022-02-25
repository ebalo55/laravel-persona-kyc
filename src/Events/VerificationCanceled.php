<?php

namespace Doinc\PersonaKyc\Events;

use Doinc\PersonaKyc\Models\Account;
use Doinc\PersonaKyc\Models\Document;
use Doinc\PersonaKyc\Models\Inquiry;
use Doinc\PersonaKyc\Models\Verification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationCanceled
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public Verification $verification
    )
    {}
}
