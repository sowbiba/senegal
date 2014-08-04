<?php
namespace Api\Sdk\Validator\Constraints;

use \Symfony\Component\Validator\Constraint;

/**
 * Class ChoicesList
 * @package Api\Sdk\Validator\Constraints
 *
 * @Annotation
 *
 */
class ChoicesList extends Constraint
{
    /**
     * @var string
     */
    public $message = "La valeur saisie n'est pas une valeur de liste valide";

    /**
     * @var array $choices choices
     *     format :
     *         [['choice_id'=> 'choice_value'], [...]]
     */
    public $choices;

    public function validatedBy()
    {
        return 'Api\Sdk\Validator\Constraints\ChoicesListValidator';
    }
}
