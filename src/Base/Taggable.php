<?php

namespace Doinc\PersonaKyc\Base;

use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Doinc\PersonaKyc\Exceptions\InvalidTagName;
use Doinc\PersonaKyc\Exceptions\PersonaAccountConflict;
use Doinc\PersonaKyc\Exceptions\PersonaRecordNotFound;
use Doinc\PersonaKyc\Models\Account;
use Doinc\PersonaKyc\PersonaErrorChecker;

trait Taggable
{
    /**
     * Check for format error in tags
     *
     * @throws InvalidTagName
     */
    private function checkTags(string|array $tag)
    {
        if (is_array($tag)) {
            foreach ($tag as $t) {
                if (strlen($t) > 255) {
                    throw new InvalidTagName();
                }
            }
        } elseif (strlen($tag) > 255) {
            throw new InvalidTagName();
        }
    }

    /**
     * Add a new tag to a persona object
     *
     * @param string $return_type Return type of this method, this must be a classname containing the `from` static method
     * @param string $compiled_url Persona request url
     * @param string $new_tag Tag to append, case-insensitive
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotFound
     * @throws InvalidTagName
     */
    protected function internalAddTag(string $return_type, string $compiled_url, string $new_tag): mixed
    {
        $this->checkTags($new_tag);

        $response = $this->baseRequest()->post(
            $compiled_url,
            [
                "meta" => [
                    "tag-name" => $new_tag
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return $return_type::from($response->json());
    }

    /**
     * Remove a tag from a Persona object
     *
     * @param string $return_type Return type of this method, this must be a classname containing the `from` static method
     * @param string $compiled_url Persona request url
     * @param string $tag Tag to remove, case-insensitive
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotFound
     * @throws InvalidTagName
     */
    protected function internalRemoveTag(string $return_type, string $compiled_url, string $tag): mixed
    {
        $this->checkTags($tag);
        $response = $this->baseRequest()->post(
            $compiled_url,
            [
                "meta" => [
                    "tag-name" => $tag
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return $return_type::from($response->json());
    }

    /**
     * Sync tags to from a Persona object
     *
     * @param string $return_type Return type of this method, this must be a classname containing the `from` static method
     * @param string $compiled_url Persona request url
     * @param string[] $tags Tag to remove, case-insensitive
     * @return Account
     * @throws InvalidModelData|PersonaAccountConflict|PersonaRecordNotFound
     */
    protected function internalSyncTags(string $return_type, string $compiled_url, array $tags): mixed
    {
        $this->checkTags($tags);
        $response = $this->baseRequest()->post(
            $compiled_url,
            [
                "meta" => [
                    "tag-name" => $tags
                ]
            ]
        );

        PersonaErrorChecker::checkErrors($response);
        return $return_type::from($response->json());
    }

    abstract public function addTag(string $identifier, string $new_tag): mixed;
    abstract public function removeTag(string $identifier, string $tag): mixed;
    abstract public function syncTags(string $identifier, array $tags): mixed;
}
