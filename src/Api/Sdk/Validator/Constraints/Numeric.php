<?php
namespace Api\Sdk\Validator\Constraints;

use \Symfony\Component\Validator\Constraints\Regex;

/**
 * Class Numeric
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class Numeric extends Regex
{
    /**
     * @var string
     */
    public $message = "La valeur saisie n'est pas une valeur numérique valide";

    public function validatedBy()
    {
        return 'Api\Sdk\Validator\Constraints\NumericValidator';
    }
}
