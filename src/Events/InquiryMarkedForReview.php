<?php

namespace Doinc\PersonaKyc\Events;

use Doinc\PersonaKyc\Models\Account;
use Doinc\PersonaKyc\Models\Document;
use Doinc\PersonaKyc\Models\Inquiry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InquiryMarkedForReview
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public Inquiry $inquiry
    )
    {}
}
