## Event List
The package emits an event from a large list of available ones when a webhook request is received.

Laravel default listener implementation can be used to listen to one or more events.


| Event name                          | Return type      |
|-------------------------------------|------------------|
| `AccountArchived`                   | `Account`        |       
| `AccountCreated`                    | `Account`        |      
| `AccountRedacted`                   | `Account`        |  
| `AccountRestored`                   | `Account`        |      
| `AccountMerged`                     | `Account`        |          
| `AccountTagAdded`                   | `Account`        |         
| `AccountTagRemoved`                 | `Account`        |    
|                                     |                  |
| `CaseCreated`                       | `array`          |         
| `CaseAssigned`                      | `array`          |         
| `CaseResolved`                      | `array`          |         
| `CaseReopened`                      | `array`          |         
| `CaseUpdated`                       | `array`          |         
| `CaseUpdated`                       | `array`          |   
|                                     |                  |         
| `DocumentCreated`                   | `Document`       |         
| `DocumentSubmitted`                 | `Document`       |         
| `DocumentProcessed`                 | `Document`       |         
| `DocumentErrored`                   | `Document`       |    
|                                     |                  |          
| `InquiryCreated`                    | `Inquiry`        |    
| `InquiryStarted`                    | `Inquiry`        |    
| `InquiryExpired`                    | `Inquiry`        |    
| `InquiryCompleted`                  | `Inquiry`        |    
| `InquiryFailed`                     | `Inquiry`        |    
| `InquiryMarkedForReview`            | `Inquiry`        |    
| `InquiryApproved`                   | `Inquiry`        |    
| `InquiryDeclined`                   | `Inquiry`        |    
| `InquiryTransitioned`               | `Inquiry`        |    
|                                     |                  |   
| `InquirySessionStarted`             | `InquirySession` |    
| `InquirySessionExpired`             | `InquirySession` |    
|                                     |                  |   
| `ReportAddressLookupReady`          | `array`          |    
| `ReportAdverseMediaMatched`         | `array`          |    
| `ReportAdverseMediaReady`           | `array`          |    
| `ReportBusinessAdverseMediaMatched` | `array`          |    
| `ReportBusinessAdverseMediaReady`   | `array`          |    
| `ReportBusinessWatchlistReady`      | `array`          |    
| `ReportBusinessWatchlistMatched`    | `array`          |    
| `ReportEmailAddressReady`           | `array`          |    
| `ReportPhoneNumberReady`            | `array`          |    
| `ReportProfileReady`                | `array`          |    
| `ReportWatchlistMatched`            | `array`          |    
| `ReportWatchlistReady`              | `array`          |    
|                                     |                  |    
| `SelfieCreated`                     | `array`          |    
| `SelfieSubmitted`                   | `array`          |    
| `SelfieProcessed`                   | `array`          |    
| `SelfieErrored`                     | `array`          |    
|                                     |                  |    
| `VerificationCreated`               | `Verification`   |    
| `VerificationPassed`                | `Verification`   |    
| `VerificationFailed`                | `Verification`   |    
| `VerificationRequiresRetry`         | `Verification`   |    
| `VerificationCanceled`              | `Verification`   |    
|                                     |                  |     
| `AccountPropertyRedacted`           | `array`          |     
