<?php

namespace Doinc\PersonaKyc\Enums;

enum DocumentStatus: string
{
    /**
     * When the individual is first asked to provide a document, the document is initiated.
     * During this time, a document can be uploaded with files before submitting it for processing.
     */
    case INITIATED = "initiated";
    /**
     * When the individual submits their document, the server processes the document.
     */
    case SUBMITTED = "submitted";
    /**
     * The server is done processing the document.
     */
    case PROCESSED = "processed";
    /**
     * If the server fails to process the document.
     */
    case ERRORED = "errored";
}
