<?php

namespace Api\Sdk\Model;

/**
 * Class UploadableInterface
 * @package Api\Sdk\Model
 *
 * This interface is used by an event to catch and handle uploadable files
 */
interface UploadableInterface
{
    public function getFile();

    public function getFilePath();

    public function setSize($size);

    public function setFilePath($filePath);
}
