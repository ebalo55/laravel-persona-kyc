<?php

namespace Doinc\PersonaKyc\Enums;

enum GovernmentIdClass: string
{
    case CITIZEN_CERTIFICATE = "cct";
    case CONSULAR_ID = "cid";
    case DRIVER_LICENSE = "dl";
    case HEALTHCARE_INSURANCE_CARD = "hic";
    case NATION_ID = "id";
    case INTERNATIONAL_PASSPORT = "ipp";
    case LONG_TERM_PASS_CARD = "ltpass";
    case MILITARY_ID = "mid";
    case MY_NUMBER_CARD = "myn";
    case NBI_CARD = "nbi";
    case NRIC = "nric";
    case OFW_ID = "ofw";
    case PAN_CARD = "pan";
    case POSTAL_ID = "pid";
    case PASSPORT = "pp";
    case PASSPORT_CARD = "ppc";
    case PERMANENT_RESIDENT_CARD = "pr";
    case RESIDENCY_PERMIT = "rp";
    case SOCIAL_SECURITY_ID = "sss";
    case UMID = "umid";
    case VOTER_ID = "vid";
    case VISA = "visa";
    case WORK_PERMIT = "wp";
    case UNKNOWN = "";
}
