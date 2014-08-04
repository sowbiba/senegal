<?php
namespace Api\Sdk\Validator\Constraints;

use Api\Sdk\Model\Field;
use \Symfony\Component\Validator\Constraint;
use \Symfony\Component\Validator\ConstraintValidator;
use \Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class NumericValidator
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class NumericValidator extends ConstraintValidator
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

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        if ($constraint->match xor preg_match($constraint->pattern, $value)) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
        }
    }
}
