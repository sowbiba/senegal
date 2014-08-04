<?php
namespace Api\Sdk\Validator\Constraints;

use Api\Sdk\Model\Field;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ChoicesListValidator
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class ChoicesListValidator extends ConstraintValidator
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

        $finalvalue = (int) $value;
        if (!is_int($finalvalue) || !($finalvalue == $value)) {
            $this->context->addViolation($constraint->message);

            return;
        }

        if (!array_key_exists($value, $constraint->choices)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
