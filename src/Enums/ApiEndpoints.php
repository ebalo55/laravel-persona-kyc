<?php
namespace Doinc\PersonaKyc\Enums;

enum ApiEndpoints: string {
    case INQUIRIES = "https://withpersona.com/api/v1/inquiries";
    case INQUIRIES_SINGLE = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:";
    case INQUIRIES_SINGLE_ADD_TAG = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:/add-tag";
    case INQUIRIES_SINGLE_REMOVE_TAG = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:/remove-tag";
    case INQUIRIES_SINGLE_SYNC_TAGS = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:/set-tags";
    case INQUIRIES_SINGLE_PDF = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:/print";
    case INQUIRIES_SINGLE_RESUME = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:/resume";
    case INQUIRIES_SINGLE_APPROVE = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:/approve";
    case INQUIRIES_SINGLE_DECLINE = "https://withpersona.com/api/v1/inquiries/:INQUIRY_ID:/decline";

    case ACCOUNTS = "https://withpersona.com/api/v1/accounts";
    case ACCOUNTS_SINGLE = "https://withpersona.com/api/v1/accounts/:ACCOUNT_ID:";
    case ACCOUNTS_SINGLE_ADD_TAG = "https://withpersona.com/api/v1/accounts/:ACCOUNT_ID:/add-tag";
    case ACCOUNTS_SINGLE_REMOVE_TAG = "https://withpersona.com/api/v1/accounts/:ACCOUNT_ID:/remove-tag";
    case ACCOUNTS_SINGLE_SYNC_TAGS = "https://withpersona.com/api/v1/accounts/:ACCOUNT_ID:/set-tags";
    case ACCOUNTS_SINGLE_MERGE = "https://withpersona.com/api/v1/accounts/:ACCOUNT_ID:/consolidate";

    case VERIFICATIONS_SINGLE = "https://withpersona.com/api/v1/verifications/:VERIFICATION_ID:";
    case VERIFICATIONS_SINGLE_PDF = "https://withpersona.com/api/v1/verifications/:VERIFICATION_ID:/print";

    case DOCUMENT_SINGLE = "https://withpersona.com/api/v1/documents/:DOCUMENT_ID:";

    case FILE_SINGLE = "https://withpersona.com/api/v1/files/:ORGANIZATION_ID:/:FILENAME:";

    case EVENTS = "https://withpersona.com/api/v1/events";
    case EVENTS_SINGLE = "https://withpersona.com/api/v1/events/:EVENT_ID:";
}
