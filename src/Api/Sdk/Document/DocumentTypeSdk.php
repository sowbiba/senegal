<?php

namespace Api\Sdk\Document;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Model\DocumentType;
use Api\Sdk\SdkInterface;

/**
 * This class lists all the high-level methods for Company objects.
 *
 * This class can only use POPO classes (\Api\Sdk\Model)
 * To use this class you have to initialize a connector (\Api\Sdk\Connector) and pass it to the constructor
 * These connectors work with POPO objects, to save an object you have to pass it
 * Only connectors can use entities (\Api\SdkBundle\Entity)
 *
 * Class DocumentTypeSdk
 * @package Api\Sdk\DocumentType
 *
 */
class DocumentTypeSdk extends AbstractSdk implements SdkInterface
{
    /**
     * Returns all existing companies
     *
     * @return array
     */
    public function getAll()
    {
        $data = $this->connector->getDocumentTypes();

        return array_map(function ($data) {
            return new DocumentType($this, $data);
        }, $data);
    }

    /**
     * @param string $classname
     *
     * @return bool
     */
    public function supports($classname)
    {
        return $classname === 'Api\Sdk\Model\DocumentType';
    }
}
