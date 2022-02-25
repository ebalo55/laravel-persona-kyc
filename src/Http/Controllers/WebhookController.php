<?php

namespace Doinc\PersonaKyc\Http\Controllers;

use Doinc\PersonaKyc\Enums\EventTypes;
use Doinc\PersonaKyc\Events\AccountArchived;
use Doinc\PersonaKyc\Events\AccountCreated;
use Doinc\PersonaKyc\Events\AccountMerged;
use Doinc\PersonaKyc\Events\AccountPropertyRedacted;
use Doinc\PersonaKyc\Events\AccountRedacted;
use Doinc\PersonaKyc\Events\AccountRestored;
use Doinc\PersonaKyc\Events\AccountTagAdded;
use Doinc\PersonaKyc\Events\AccountTagRemoved;
use Doinc\PersonaKyc\Events\CaseAssigned;
use Doinc\PersonaKyc\Events\CaseCreated;
use Doinc\PersonaKyc\Events\CaseReopened;
use Doinc\PersonaKyc\Events\CaseResolved;
use Doinc\PersonaKyc\Events\CaseUpdated;
use Doinc\PersonaKyc\Events\DocumentCreated;
use Doinc\PersonaKyc\Events\DocumentErrored;
use Doinc\PersonaKyc\Events\DocumentProcessed;
use Doinc\PersonaKyc\Events\DocumentSubmitted;
use Doinc\PersonaKyc\Events\InquiryApproved;
use Doinc\PersonaKyc\Events\InquiryCompleted;
use Doinc\PersonaKyc\Events\InquiryCreated;
use Doinc\PersonaKyc\Events\InquiryDeclined;
use Doinc\PersonaKyc\Events\InquiryExpired;
use Doinc\PersonaKyc\Events\InquiryFailed;
use Doinc\PersonaKyc\Events\InquiryMarkedForReview;
use Doinc\PersonaKyc\Events\InquirySessionExpired;
use Doinc\PersonaKyc\Events\InquirySessionStarted;
use Doinc\PersonaKyc\Events\InquiryStarted;
use Doinc\PersonaKyc\Events\InquiryTransitioned;
use Doinc\PersonaKyc\Events\ReportAddressLookupReady;
use Doinc\PersonaKyc\Events\ReportAdverseMediaMatched;
use Doinc\PersonaKyc\Events\ReportAdverseMediaReady;
use Doinc\PersonaKyc\Events\ReportBusinessAdverseMediaMatched;
use Doinc\PersonaKyc\Events\ReportBusinessAdverseMediaReady;
use Doinc\PersonaKyc\Events\ReportBusinessWatchlistMatched;
use Doinc\PersonaKyc\Events\ReportBusinessWatchlistReady;
use Doinc\PersonaKyc\Events\ReportEmailAddressReady;
use Doinc\PersonaKyc\Events\ReportPhoneNumberReady;
use Doinc\PersonaKyc\Events\ReportProfileReady;
use Doinc\PersonaKyc\Events\ReportWatchlistMatched;
use Doinc\PersonaKyc\Events\ReportWatchlistReady;
use Doinc\PersonaKyc\Events\SelfieCreated;
use Doinc\PersonaKyc\Events\SelfieErrored;
use Doinc\PersonaKyc\Events\SelfieProcessed;
use Doinc\PersonaKyc\Events\SelfieSubmitted;
use Doinc\PersonaKyc\Events\VerificationCanceled;
use Doinc\PersonaKyc\Events\VerificationCreated;
use Doinc\PersonaKyc\Events\VerificationFailed;
use Doinc\PersonaKyc\Events\VerificationPassed;
use Doinc\PersonaKyc\Events\VerificationRequiresRetry;
use Doinc\PersonaKyc\Events\VerificationSubmitted;
use Doinc\PersonaKyc\Models\Event;
use Doinc\PersonaKyc\Models\StoredEvent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Session\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class WebhookController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected array $whitelisted_ips = [
        "35.232.44.140",
        "34.69.131.123",
        "34.67.4.225",
    ];

    public function handle(Request $request)
    {
        // first round of checks: ip is in whitelist and signature header is present
        if (
            $request->headers->has("X-Forwarded-For") &&
            in_array($request->header("X-Forwarded-For"), $this->whitelisted_ips, true) &&
            $request->headers->has("Persona-Signature")
        ) {
            [$time, $v1] = explode(",", $request->header("Persona-Signature"));
            $time = Str::replace("t=", "", $time);
            $v1 = Str::replace("v1=", "", $v1);
            $secret = env("PERSONA_WEBHOOK_SECRET", "");

            $regenerated_signature = hash_hmac("sha256", "$time.{$request->getContent()}", $secret);

            // second round of checks: if hashes match each other than proceed with event handling
            if ($v1 === $regenerated_signature) {
                $event = Event::from($request->all());

                // emit events only if not previously received the event, this avoids duplication
                if (is_null(StoredEvent::where("signature", $event->signature())->first())) {
                    StoredEvent::create([
                        "signature" => $event->signature(),
                        "generation_timestamp" => $time
                    ]);
                    switch ($event->event_type) {
                        case EventTypes::ACCOUNT_ARCHIVED:
                            AccountArchived::dispatch($event->payload());
                            break;
                        case EventTypes::ACCOUNT_CREATED:
                            AccountCreated::dispatch($event->payload());
                            break;
                        case EventTypes::ACCOUNT_REDACTED:
                            AccountRedacted::dispatch($event->payload());
                            break;
                        case EventTypes::ACCOUNT_RESTORED:
                            AccountRestored::dispatch($event->payload());
                            break;
                        case EventTypes::ACCOUNT_CONSOLIDATED:
                            AccountMerged::dispatch($event->payload());
                            break;
                        case EventTypes::ACCOUNT_TAG_ADDED:
                            AccountTagAdded::dispatch($event->payload());
                            break;
                        case EventTypes::ACCOUNT_TAG_REMOVED:
                            AccountTagRemoved::dispatch($event->payload());
                            break;

                        case EventTypes::CASE_CREATED:
                            CaseCreated::dispatch($event->payload());
                            break;
                        case EventTypes::CASE_ASSIGNED:
                            CaseAssigned::dispatch($event->payload());
                            break;
                        case EventTypes::CASE_RESOLVED:
                            CaseResolved::dispatch($event->payload());
                            break;
                        case EventTypes::CASE_REOPENED:
                            CaseReopened::dispatch($event->payload());
                            break;
                        case EventTypes::CASE_UPDATED:
                            CaseUpdated::dispatch($event->payload());
                            break;

                        case EventTypes::DOCUMENT_CREATED:
                            DocumentCreated::dispatch($event->payload());
                            break;
                        case EventTypes::DOCUMENT_SUBMITTED:
                            DocumentSubmitted::dispatch($event->payload());
                            break;
                        case EventTypes::DOCUMENT_PROCESSED:
                            DocumentProcessed::dispatch($event->payload());
                            break;
                        case EventTypes::DOCUMENT_ERRORED:
                            DocumentErrored::dispatch($event->payload());
                            break;

                        case EventTypes::INQUIRY_CREATED:
                            InquiryCreated::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_STARTED:
                            InquiryStarted::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_EXPIRED:
                            InquiryExpired::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_COMPLETED:
                            InquiryCompleted::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_FAILED:
                            InquiryFailed::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_MARKED_FOR_REVIEW:
                            InquiryMarkedForReview::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_APPROVED:
                            InquiryApproved::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_DECLINED:
                            InquiryDeclined::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_TRANSITIONED:
                            InquiryTransitioned::dispatch($event->payload());
                            break;

                        case EventTypes::INQUIRY_SESSION_STARTED:
                            InquirySessionStarted::dispatch($event->payload());
                            break;
                        case EventTypes::INQUIRY_SESSION_EXPIRED:
                            InquirySessionExpired::dispatch($event->payload());
                            break;

                        case EventTypes::REPORT_ADDRESS_LOOKUP_READY:
                            ReportAddressLookupReady::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_ADVERSE_MEDIA_MATCHED:
                            ReportAdverseMediaMatched::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_ADVERSE_MEDIA_READY:
                            ReportAdverseMediaReady::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_BUSINESS_ADVERSE_MEDIA_MATCHED:
                            ReportBusinessAdverseMediaMatched::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_BUSINESS_ADVERSE_MEDIA_READY:
                            ReportBusinessAdverseMediaReady::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_BUSINESS_WATCHLIST_READY:
                            ReportBusinessWatchlistReady::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_BUSINESS_WATCHLIST_MATCHED:
                            ReportBusinessWatchlistMatched::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_EMAIL_ADDRESS_READY:
                            ReportEmailAddressReady::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_PHONE_NUMBER_READY:
                            ReportPhoneNumberReady::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_PROFILE_READY:
                            ReportProfileReady::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_WATCHLIST_MATCHED:
                            ReportWatchlistMatched::dispatch($event->payload());
                            break;
                        case EventTypes::REPORT_WATCHLIST_READY:
                            ReportWatchlistReady::dispatch($event->payload());
                            break;

                        case EventTypes::SELFIE_CREATED:
                            SelfieCreated::dispatch($event->payload());
                            break;
                        case EventTypes::SELFIE_SUBMITTED:
                            SelfieSubmitted::dispatch($event->payload());
                            break;
                        case EventTypes::SELFIE_PROCESSED:
                            SelfieProcessed::dispatch($event->payload());
                            break;
                        case EventTypes::SELFIE_ERRORED:
                            SelfieErrored::dispatch($event->payload());
                            break;

                        case EventTypes::VERIFICATION_CREATED:
                            VerificationCreated::dispatch($event->payload());
                            break;
                        case EventTypes::VERIFICATION_SUBMITTED:
                            VerificationSubmitted::dispatch($event->payload());
                            break;
                        case EventTypes::VERIFICATION_PASSED:
                            VerificationPassed::dispatch($event->payload());
                            break;
                        case EventTypes::VERIFICATION_FAILED:
                            VerificationFailed::dispatch($event->payload());
                            break;
                        case EventTypes::VERIFICATION_REQUIRES_RETRY:
                            VerificationRequiresRetry::dispatch($event->payload());
                            break;
                        case EventTypes::VERIFICATION_CANCELED:
                            VerificationCanceled::dispatch($event->payload());
                            break;

                        case EventTypes::ACCOUNT_PROPERTY_REDACTED:
                            AccountPropertyRedacted::dispatch($event->payload());
                            break;
                    }
                }
            }
        }
    }
}
