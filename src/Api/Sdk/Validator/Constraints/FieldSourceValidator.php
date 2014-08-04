<?php
namespace Api\Sdk\Validator\Constraints;

use Api\Sdk\Model\Field;
use Api\Sdk\Model\RevisionFieldSource;
use \Symfony\Component\Validator\Constraint;
use \Symfony\Component\Validator\ConstraintValidator;

/**
 * Class FieldSourceValidator
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class FieldSourceValidator extends ConstraintValidator
{
    /**
     * @param int                                     $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value || Field::VALUE_NA == $value) {
            return;
        }

        $field    = $constraint->field;
        $revision = $constraint->revision;
        $source   = $revision->getFieldSource($field);

        if (!($source instanceof RevisionFieldSource)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
