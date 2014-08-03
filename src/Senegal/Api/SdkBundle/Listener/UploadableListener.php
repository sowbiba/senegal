<?php

namespace Senegal\Api\SdkBundle\Listener;

use Pfd\Sdk\Event\ModelEvent;
use Pfd\Sdk\Model\UploadableInterface;
use Senegal\Api\SdkBundle\Tools\UploadedFileSlugifier;

/**
 * Class UploadableListener
 * @package Pfd\ContractBundle\Listener
 *
 * @link http://doc.si.profideo.com/?p=460
 */
class UploadableListener
{
    /**
     * @var UploadedFileSlugifier
     */
    private $slugifier;

    /**
     * @param UploadedFileSlugifier $slugifier
     */
    public function __construct(UploadedFileSlugifier $slugifier)
    {
        $this->slugifier = $slugifier;
    }

    /**
     * @param ModelEvent $args
     */
    public function preCreate(ModelEvent $args)
    {
        $object = $args->getData();

        if ($object instanceof UploadableInterface) {
            $this->upload($object);
        }
    }

    /**
     * @param ModelEvent $args
     */
    public function preUpdate(ModelEvent $args)
    {
        $object = $args->getData();

        if ($object instanceof UploadableInterface) {

            if (is_object($object->getFile())) {
                $this->remove($object);
            }
            $this->upload($object);
        }
    }

    /**
     * @param ModelEvent $args
     */
    public function preDelete(ModelEvent $args)
    {
        $object = $args->getData();

        if ($object instanceof UploadableInterface) {
            $this->remove($object);
        }
    }

    /**
     * Slugify file name uploaded and move it to upload directory
     *
     * @return string relative path
     */
    private function upload(UploadableInterface $object)
    {
        $file = $object->getFile();

        if (!is_object($file)) {
            return;
        }

        $this->slugifier->rename($file);

        $fileDir  = $this->slugifier->getFileDir();
        $fileName = $this->slugifier->getFileName();

        $file->move($fileDir, $fileName);

        $filePath = $this->slugifier->getRelativeFilePath();

        if (!$filePath) {
            return;
        }

        $size = $file->getClientSize();

        $object->setSize($size);
        $object->setFilePath($filePath);
    }

    /**
     * Remove file in system
     */
    private function remove(UploadableInterface $object)
    {
        $filePath = $this->slugifier->getWebDir() . $object->getFilePath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
