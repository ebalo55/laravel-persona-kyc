<?php

namespace Doinc\PersonaKyc\Base;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
use Doinc\PersonaKyc\PersonaErrorChecker;

trait Redactable
{
    /**
     * Permanently deletes personally identifiable information for a given identifier
     *
     * This action cannot be reverted.
     * This is made to be used to comply with privacy regulations such as GDPR/CCPA or
     * to enforce data privacy
     *
     * NOTE: The object still exists and is still updatable after redaction
     *
     * @param string $return_type Return type of this method, this must be a classname containing the `from` static method
     * @param string $compiled_url Persona request url
     * @return mixed
     * @throws InvalidModelData|PersonaRecordNotFound
     */
    protected function internalRedact(string $return_type, string $compiled_url): mixed
    {
        $response = $this->baseRequest()->delete($compiled_url);

        PersonaErrorChecker::checkErrors($response);
        return $return_type::from($response->json());
    }

    abstract public function redact(string $identifier): mixed;
}
