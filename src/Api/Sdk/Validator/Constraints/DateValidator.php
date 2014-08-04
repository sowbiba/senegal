<?php
namespace Api\Sdk\Validator\Constraints;

use Api\Sdk\Model\Field;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ChoicesListValidator
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class DateValidator extends ConstraintValidator
{
    const PATTERN = '/^(\d{2})\/(\d{2})\/(\d{4})$/';

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value || $value instanceof \DateTime || Field::VALUE_NA == $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        if (!preg_match(static::PATTERN, $value, $matches) || !checkdate($matches[2], $matches[1], $matches[3])) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
        }
    }
}
