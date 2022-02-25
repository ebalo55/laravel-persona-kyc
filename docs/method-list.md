## Main methods list
All the package functionality can be accessed instantiating the class `\Doinc\PersonaKyc\Persona` via the `init()` method.
The returned class exposes the following methods.

| Method signature  | Description                                              |
|-------------------|----------------------------------------------------------|
| `accounts()`      | Access all the [accounts]() related functionalities      |       
| `inquiries()`     | Access all the [inquiries]() related functionalities     |      
| `verifications()` | Access all the [verifications]() related functionalities |  
| `documents()`     | Access all the [documents]() related functionalities     |      
| `files()`         | Access all the [files]() related functionalities         |          
| `events()`        | Access all the [events]() related functionalities        |         

## Sub methods list

Optional parameters are omitted.

### Accounts
* `create(string) -> Account`: Creates a new account to be used in inquiries.
* `list() -> PaginatedAccounts`: Returns a list of accounts
* `get(string) -> Account`: Returns the account identified by given id
* `redact(string) -> Account`: Permanently deletes personally identifiable information for a given account
  This action cannot be reverted.<br>
  This is made to be used to comply with privacy regulations such as GDPR/CCPA or
  to enforce data privacy.<br>
  **NOTE**: Account still exists and is still updatable after redaction
* `update(string, string) -> Account`: Updates the information linked to an account
* `addTag(string, string) -> Account`: Add a new tag to an account
* `removeTag(string, string) -> Account`: Remove a tag from an account
* `syncTags(string, string[]) -> Account`: Sync tags to from an account
* `merge(string, string[]) -> Account`: Merges several source Accounts' information into one target Account.<br>
  Any Inquiry, Verification, Report and Document associated with the source Account will be
  transferred over to the destination Account.<br>
  However, the Account's attributes will not be transferred.<br>
  **NOTE**: This endpoint can be used to clean up duplicate Accounts.<br>
  **NOTE**: A source account can only be consolidated once. Afterwards, the source account will be archived.

### Inquiries
* `create(string) -> Inquiry`: Creates a new inquiry on persona.<br>
  As inquiry are instances of identity verification performed by a user, creating an inquiry means
  to request a new identity verification for a given user.<br>
  In order to use the full power of Persona inquiries will always be created using reference_id, this means
  that all actions and requests of a user will be grouped in an account leading to a full profile of the user.
* `list() -> PaginatedInquiries`: Returns a list of inquiries
* `get(string) -> Inquiry`: Returns the inquiry identified by given id
* `redact(string) -> Inquiry`: Permanently deletes personally identifiable information for a given inquiry.<br>
  This action cannot be reverted. <br>
  This is made to be used to comply with privacy regulations such as GDPR/CCPA or
  to enforce data privacy
* `addTag(string, string) -> Inquiry`: Add a new tag to an inquiry
* `removeTag(string, string) -> Inquiry`: Remove a tag from an inquiry
* `syncTags(string, string[]) -> Inquiry`: Sync tags to an inquiry
* `getPdf(string, RequestMode) -> string | StreamedResponse`: Retrieve the inquiry pdf.<br>
  Depending on the requested mode returns the raw binary representation or a file download
* `update(string) -> Inquiry`: Updates the information linked to an inquiry
* `resume(string) -> Inquiry`: Resume an existing inquiry.<br>
  When resuming pending inquiries the session token generated here should be provided
* `approve(string) -> Inquiry`: Approves an existing inquiry. <br>
  This method triggers workflows and webhooks on Persona's side
* `decline(string) -> Inquiry`: Decline an existing inquiry. <br>
  This method triggers workflows and webhooks on Persona's side

### Verifications
* `get(string) > Verification`: Returns the verification identified by given id
* `getPdf(string, RequestMode) -> string | StreamedResponse`: Retrieve the verification pdf.<br>
  Depending on the requested mode returns the raw binary representation or a file download

### Documents
* `get(string) -> Document`: Returns the document identified by given id

### Files
* `download(string, string, RequestMode) -> string | StreamedResponse`: Retrieve the file pdf identified by the company id and its filename. <br>
  Depending on the requested mode returns the raw binary representation or a file download
* `downloadFromUrl(string, RequestMode) -> string | StreamedResponse`: Retrieve the file pdf identified by the company id and its filename.<br>
  Depending on the requested mode returns the raw binary representation or a file download.<br>
  ALERT: Abusing this endpoint to request external non-Persona endpoints will leak your bearer token,
         always check the endpoint you're requesting and avoid any unsafe domain

### Events
* `get(string) -> Event`: Returns the event identified by given id
* `list() -> PaginatedEvents`: Returns a list of events
