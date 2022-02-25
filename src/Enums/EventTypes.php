<?php

namespace Doinc\PersonaKyc\Enums;

enum EventTypes: string
{
    /**
     * Occurs whenever an account is created.
     */
    case ACCOUNT_CREATED = "account.created";
    /**
     * Occurs whenever an account is redacted.
     */
    case ACCOUNT_REDACTED = "account.redacted";
    /**
     * Occurs whenever an account is archived.
     */
    case ACCOUNT_ARCHIVED = "account.archived";
    /**
     * Occurs whenever an account is un-archived.
     */
    case ACCOUNT_RESTORED = "account.restored";
    /**
     * Occurs when the account was combined with another account.
     */
    case ACCOUNT_CONSOLIDATED = "account.consolidated";
    /**
     * Occurs when a tag was added to an account.
     */
    case ACCOUNT_TAG_ADDED = "account.tag-added";
    /**
     * Occurs when a tag was removed from an account
     */
    case ACCOUNT_TAG_REMOVED = "account.tag-removed";

    /**
     * Occurs when a case is created.
     */
    case CASE_CREATED = "case.created";
    /**
     * Occurs when a case is assigned.
     */
    case CASE_ASSIGNED = "case.assigned";
    /**
     * Occurs when a case is resolved.
     */
    case CASE_RESOLVED = "case.resolved";
    /**
     * Occurs when a case is reopened.
     */
    case CASE_REOPENED = "case.reopened";
    /**
     * Occurs when a case is updated.
     */
    case CASE_UPDATED = "case.updated";

    /**
     * Occurs whenever a document is created.
     */
    case DOCUMENT_CREATED = "document.created";
    /**
     * Occurs whenever a document is submitted.
     */
    case DOCUMENT_SUBMITTED = "document.submitted";
    /**
     * Occurs whenever a document is processed.
     */
    case DOCUMENT_PROCESSED = "document.processed";
    /**
     * Occurs whenever a document errors while processing.
     */
    case DOCUMENT_ERRORED = "document.errored";

    /**
     * Occurs whenever an inquiry is created.
     */
    case INQUIRY_CREATED = "inquiry.created";
    /**
     * Occurs whenever an inquiry is started. This happens the moment a verification is created or submitted
     * on an inquiry
     */
    case INQUIRY_STARTED = "inquiry.started";
    /**
     * Occurs when an inquiry expires. The default expiry is 24 hours.
     */
    case INQUIRY_EXPIRED = "inquiry.expired";
    /**
     * Occurs whenever an inquiry completes all the configured verifications.
     */
    case INQUIRY_COMPLETED = "inquiry.completed";
    /**
     * Occurs whenever an inquiry exceeds the configured number of verifications.
     */
    case INQUIRY_FAILED = "inquiry.failed";
    /**
     * Occurs when an inquiry was marked for review either through Workflows or the API.
     */
    case INQUIRY_MARKED_FOR_REVIEW = "inquiry.marked-for-review";
    /**
     * Occurs whenever an inquiry is approved manually in the dashboard or automatically through Workflows or the API.
     */
    case INQUIRY_APPROVED = "inquiry.approved";
    /**
     * Occurs when an inquiry is declined manually in the dashboard or automatically through Workflows or the API.
     */
    case INQUIRY_DECLINED = "inquiry.declined";

    case INQUIRY_TRANSITIONED = "inquiry.transitioned";

    /**
     * Occurs whenever a user starts a session on an inquiry with a device.
     * Multiple devices will each spawn a session.
     */
    case INQUIRY_SESSION_STARTED = "inquiry-session.started";
    /**
     * Occurs when a session expires.
     */
    case INQUIRY_SESSION_EXPIRED = "inquiry-session.expired";

    /**
     * Occurs when an address lookup report has completed processing.
     */
    case REPORT_ADDRESS_LOOKUP_READY = "report/address-lookup.ready";
    /**
     * Occurs when an adverse media report has matched against at least one adverse media source as specified within configuration.
     */
    case REPORT_ADVERSE_MEDIA_MATCHED = "report/adverse-media.matched";
    /**
     * Occurs when an adverse media report has completed processing.
     */
    case REPORT_ADVERSE_MEDIA_READY = "report/adverse-media.ready";
    /**
     * Occurs when a business adverse media report has matched against at least one adverse media source as specified within configuration.
     */
    case REPORT_BUSINESS_ADVERSE_MEDIA_MATCHED = "report/business-adverse-media.matched";
    /**
     * Occurs when a business adverse media report has completed processing.
     */
    case REPORT_BUSINESS_ADVERSE_MEDIA_READY = "report/business-adverse-media.ready";
    /**
     * Occurs when a business watchlist report has completed processing.
     */
    case REPORT_BUSINESS_WATCHLIST_READY = "report/business-watchlist.ready";
    /**
     * Occurs when a business watchlist report has matched against a watchlist as specified within configuration.
     */
    case REPORT_BUSINESS_WATCHLIST_MATCHED = "report/business-watchlist.matched";
    /**
     * Occurs when an email address report has completed processing.
     */
    case REPORT_EMAIL_ADDRESS_READY = "report/email-address.ready";
    /**
     * Occurs when a phone number report is ready.
     */
    case REPORT_PHONE_NUMBER_READY = "report/phone-number.ready";
    /**
     * Occurs when a profile report is ready.
     */
    case REPORT_PROFILE_READY = "report/profile.ready";
    /**
     * Occurs when a watchlist report has matched against a watchlist as specified within configuration.
     */
    case REPORT_WATCHLIST_MATCHED = "report/watchlist.matched";
    /**
     * Occurs when a watchlist report has completed processing.
     */
    case REPORT_WATCHLIST_READY = "report/watchlist.ready";

    /**
     * Occurs whenever a selfie is created.
     */
    case SELFIE_CREATED = "selfie.created";
    /**
     * Occurs whenever a selfie is submitted.
     */
    case SELFIE_SUBMITTED = "selfie.submitted";
    /**
     * Occurs whenever a selfie is processed.
     */
    case SELFIE_PROCESSED = "selfie.processed";
    /**
     * Occurs whenever a selfie's processing has errored.
     */
    case SELFIE_ERRORED = "selfie.errored";

    /**
     * Occurs whenever a verification is created.
     */
    case VERIFICATION_CREATED = "verification.created";
    /**
     * Occurs when a verification is submitted.
     */
    case VERIFICATION_SUBMITTED = "verification.submitted";
    /**
     * Occurs when a verification passes.
     */
    case VERIFICATION_PASSED = "verification.passed";
    /**
     * Occurs when a verification fails.
     */
    case VERIFICATION_FAILED = "verification.failed";
    /**
     * Occurs when a verification requires the individual to retry.
     */
    case VERIFICATION_REQUIRES_RETRY = "verification.requires-retry";
    /**
     * Occurs when a verification gets cancelled.
     */
    case VERIFICATION_CANCELED = "verification.canceled";

    /**
     * Occurs when an account property gets redacted.
     */
    case ACCOUNT_PROPERTY_REDACTED = "account-property.redacted";
}
