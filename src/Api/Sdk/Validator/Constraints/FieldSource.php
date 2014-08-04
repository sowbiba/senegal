<?php
namespace Api\Sdk\Validator\Constraints;

use \Symfony\Component\Validator\Constraint;
use Api\Sdk\Model\Field;
use Api\Sdk\Model\Revision;

/**
 * Class FieldSource
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class FieldSource extends Constraint
{
    /**
     * @var string
     */
    public $message = "La source est manquante ou mal renseignée";

    /**
     * @var Field
     */
    public $field;

    /**
     * @var Revision
     */
    public $revision;

    public function validatedBy()
    {
        return 'Api\Sdk\Validator\Constraints\FieldSourceValidator';
    }
}
