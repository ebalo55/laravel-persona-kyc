<?php

namespace Doinc\PersonaKyc;

use Doinc\PersonaKyc\Base\PersonaBaseInitializer;

class Persona
{
    use PersonaBaseInitializer;

    /**
     * Access all the accounts related functionalities
     *
     * @return PersonaAccounts
     */
    public function accounts(): PersonaAccounts {
        return PersonaAccounts::init();
    }

    /**
     * Access all the inquiries related functionalities
     *
     * @return PersonaInquiries
     */
    public function inquiries(): PersonaInquiries {
        return PersonaInquiries::init();
    }

    /**
     * Access all the verifications related functionalities
     *
     * @return PersonaVerification
     */
    public function verifications(): PersonaVerification {
        return PersonaVerification::init();
    }

    /**
     * Access all the documents related functionalities
     *
     * @return PersonaDocument
     */
    public function documents(): PersonaDocument {
        return PersonaDocument::init();
    }

    /**
     * Access all the files related functionalities
     *
     * @return PersonaFile
     */
    public function files(): PersonaFile {
        return PersonaFile::init();
    }

    /**
     * Access all the events related functionalities
     *
     * @return PersonaEvent
     */
    public function events(): PersonaEvent {
        return PersonaEvent::init();
    }
}
