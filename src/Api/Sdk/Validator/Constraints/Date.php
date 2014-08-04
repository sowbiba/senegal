<?php
namespace Api\Sdk\Validator\Constraints;

use \Symfony\Component\Validator\Constraint;

/**
 * Class Date
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class Date extends Constraint
{
    /**
     * @var string
     */
    public $message = "La date saisie n'est pas une date valide";

    public function validatedBy()
    {
        return 'Api\Sdk\Validator\Constraints\DateValidator';
    }
}
