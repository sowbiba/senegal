<?php

namespace Senegal\Api\SdkBundle\Tools;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadedFileSlugifier
 * @package Senegal\Api\SdkBundle\Tools
 *
 * @link http://doc.si.profideo.com/?p=530
 */
class UploadedFileSlugifier
{
    protected $uploadDir;
    protected $webDir;
    protected $fileName;

    /**
     * @param string $uploadDir
     * @param string $webDir
     */
    public function __construct($uploadDir, $webDir)
    {
        $this->uploadDir = $uploadDir;
        $this->webDir    = $webDir;
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function rename(UploadedFile $file)
    {
        $this->fileName    = $file->getClientOriginalName();
        $extension         = pathinfo($this->fileName, PATHINFO_EXTENSION);
        $this->fileName    = pathinfo($this->fileName, PATHINFO_FILENAME);
        $slugifiedFileName = $this->slugify($this->fileName);
        $newFileName       = sprintf('%s.%s', $slugifiedFileName, $extension);
        $this->setFileName($newFileName);

        return $newFileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Create a slug from text
     *
     * @param string $text
     * @param string $wildcard
     *
     * @return string
     */
    public function slugify($text, $wildcard = '-')
    {
        $text = \preg_replace('~[^\\pL\d]+~u', $wildcard, $text);
        $text = \trim($text, '-');

        if (\function_exists('iconv')) {
            $text = \iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        $text = \strtolower($text);
        $text = \preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Return file name slugified
     *
     * @return type
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Return absolute path of file directory
     *
     * @return string
     */
    public function getFileDir()
    {
        return $this->webDir . $this->uploadDir;
    }

    /**
     * Return absolute file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->getFileDir() . $this->getFileName();
    }

    /**
     * Return filename concatenated to upload directory
     *
     * @return string
     */
    public function getRelativeFilePath()
    {
        return $this->uploadDir . $this->getFileName();
    }

    /**
     * Return web directory
     *
     * @return string
     */
    public function getWebDir()
    {
        return $this->webDir;
    }
}
