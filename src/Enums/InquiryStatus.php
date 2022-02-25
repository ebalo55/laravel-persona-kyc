<?php

namespace Doinc\PersonaKyc\Enums;

enum InquiryStatus: string
{
    /**
     * The individual started the inquiry.
     */
    case CREATED = "created";
    /**
     * The individual submitted a verification within the inquiry.
     */
    case PENDING = "pending";
    /**
     * The individual passed all required verifications within the inquiry.
     */
    case COMPLETED = "completed";
    /**
     * Optional status applied via api using custom decisioning logic.
     */
    case APPROVED = "approved";
    /**
     * Optional status applied via api using custom decisioning logic.
     */
    case DECLINED = "declined";
    /**
     * The individual did not complete the inquiry within 24 hours.
     */
    case EXPIRED = "expired";
    /**
     * The individual exceeded the allowed number of verification attempts on the inquiry and cannot continue.
     */
    case FAILED = "failed";

    case ACTIVE = "active";
}
