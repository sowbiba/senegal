<?php
namespace Api\Sdk\Validator\Constraints;

use Api\Sdk\SdkInterface;
use Api\SdkBundle\Tools\UploadedFileSlugifier;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ExistingFilenameValidator
 * @package Api\ContractBundle\Validator\Constraints
 *
 * @Annotation
 *
 * @link http://doc.si.profideo.com/?p=534
 */
class ExistingFilenameValidator extends ConstraintValidator
{
    /**
     * @var
     */
    private $slugifier;

    /**
     * @var \Api\Sdk\SdkInterface
     */
    private $sdk;

    /**
     * @param UploadedFileSlugifier $slugifier
     * @param SdkInterface          $sdk
     */
    public function __construct(UploadedFileSlugifier $slugifier, SdkInterface $sdk)
    {
        $this->slugifier = $slugifier;
        $this->sdk       = $sdk;
    }

    /**
     * @param mixed      $file
     * @param Constraint $constraint
     */
    public function validate($file, Constraint $constraint)
    {
        if (null === $file || !$file instanceof UploadedFile) {
            return;
        }

        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        if (!$extension) {
            return;
        }

        $this->slugifier->rename($file);

        $fileName   = $file->getClientOriginalName();
        $filePath   = $this->slugifier->getRelativeFilePath();
        $document   = $this->sdk->alreadyExists($filePath);

        if ($document) {
            $this->context->addViolation($constraint->message, array('%string%' => $fileName));
        }
    }

}
