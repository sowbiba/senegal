<?php

namespace Api\SdkBundle\Tests\Tools;

use Api\SdkBundle\Tools\UploadedFileSlugifier;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class SlugifierTest
 *
 * @package Api\SdkBundle\Tests\Tools
 */
class UploadedFileSlugifierTest extends \PHPUnit_Framework_TestCase
{
    const UPLOAD_DIR = '/import/documents/';
    const WEB_DIR    = '/web/';

    private $slugifier;

    public function setUp()
    {
        $this->slugifier = new UploadedFileSlugifier(self::UPLOAD_DIR, self::WEB_DIR);
    }

    /**
     * @return array
     */
    public function dataProviderSlug()
    {
        return [
            ['c\'est un super test', 'c-est-un-super-test'],
            ['1, 2, 3, four|five|six', '1-2-3-four-five-six'],
            ['éè$^:=;)àç!', 'ee-ac'],
            ['élémentaire ça fonctionne', 'elementaire-ca-fonctionne'],
            ['mon nom de fichier (avec parenthèses)', 'mon-nom-de-fichier-avec-parentheses']
        ];
    }

    /**
     * @return array
     */
    public function dataProviderFilename()
    {
        return [
            ['c\'est un super test.pdf', 'c-est-un-super-test.pdf', 'application/pdf'],
            ['1, 2, 3, four five six.jpg', '1-2-3-four-five-six.jpg', 'image/jpeg'],
            ['bla.bla.zip', 'bla-bla.zip', 'application/zip'],
            ['élémentaire ça fonctionne.mp3', 'elementaire-ca-fonctionne.mp3', 'audio/mp3'],
            ['mon nom de fichier (avec parenthèses).pdf', 'mon-nom-de-fichier-avec-parentheses.pdf', 'application/pdf']
        ];
    }

        /**
     * @dataProvider dataProviderSlug
     *
     * @param $from
     * @param $to
     */
    public function testStringIsCorrectlySlugified($from, $to)
    {
        $slug = $this->slugifier->slugify($from);

        $this->assertEquals($slug, $to);
    }

    public function testFileDirIdCorrectlyComposed()
    {
        $dir = self::WEB_DIR . self::UPLOAD_DIR;

        $this->assertEquals($this->slugifier->getFileDir(), $dir);
    }

    public function testFilePathIsCorrectlyComposed()
    {
        $file     = 'popo.pdf';
        $filePath = self::WEB_DIR . self::UPLOAD_DIR . $file;

        $this->slugifier->setFileName($file);

        $this->assertEquals($this->slugifier->getFilePath(), $filePath);
    }

    /**
     * @dataProvider dataProviderFileName
     *
     * @param $from
     * @param $to
     * @param $mimetype
     */
    public function testFileNameUsCorrectlyComposed($from, $to, $mimetype)
    {
        $filePath = sprintf('%s/Fixtures/%s', __DIR__, $from);

        if (file_exists($filePath)) {
            $file = new UploadedFile(
                $filePath,
                $from,
                $mimetype,
                null,
                null
            );

            $newFileName = $this->slugifier->rename($file);

            $this->assertEquals($newFileName, $to);
        }
    }
}
