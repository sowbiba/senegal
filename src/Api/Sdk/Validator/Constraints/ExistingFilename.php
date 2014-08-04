<?php
namespace Api\Sdk\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ExistingFilename
 * @package Api\ContractBundle\Validator\Constraints
 *
 * @Annotation
 *
 * @link http://doc.si.profideo.com/?p=534
 */
class ExistingFilename extends Constraint
{
    /**
     * @var string
     */
    public $message = "Le nom de fichiers '%string%' est déja utilisé";

    public function validatedBy()
    {
        return 'existing_filename';
    }
}
