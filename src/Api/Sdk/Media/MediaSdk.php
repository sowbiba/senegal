<?php

namespace Api\Sdk\Media;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Model\Media;
use Api\Sdk\SdkInterface;

class MediaSdk extends AbstractSdk implements SdkInterface
{
    public function getAll()
    {
        $data = $this->connector->getAll();

        return array_map(function ($data) {
            return new Media($this, $data);
        }, $data);
    }

    /**
     * @param string $classname
     *
     * @return bool
     */
    public function supports($classname)
    {
        return $classname === 'Api\Sdk\Model\Media';
    }

}
