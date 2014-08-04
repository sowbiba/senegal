<?php

namespace Api\Sdk\Validator\Constraints;

// Custom constraint
use Api\Sdk\Model\Field;

/**
 * Class FieldConstraints
 */
class FieldConstraints
{
    /**
     * Cascade creation of constraints and return array
     *
     * @param Field $field
     *
     * @return array
     */
    public function getConstraints(Field $field)
    {
        $constraints = array();

        switch ($field->getTypeId()) {
            case Field::TYPE_DATE:
                $constraints[] = new Date(array(
                    'message' => sprintf("La valeur saisie n'est pas une date valide pour le champ #%d", $field->getId())
                ));
                break;

            case Field::TYPE_NUMERIC:
                // This regexp is a bit more complicated than expected, because it does two things:
                // - check the format of a number
                // - check its maximum value (here maximum one hundred billions)
                $constraints[] = new Numeric(array(
                    'pattern' => '/^[0-9]{1,11}([.,][0-9]{0,9}){0,1}$/',
                    'message' => sprintf("La valeur saisie n'est pas une valeur numérique valide pour le champ #%d. Le nombre doit être inférieur à 100 milliards, et ne peut avoir que 9 chiffres après la virgule.", $field->getId()),
                ));
                break;

            case Field::TYPE_LIST:
                $constraints[] = new ChoicesList(array(
                    'choices' => $field->getChoices(),
                    'message' => sprintf("La valeur saisie n'est pas une valeur de liste valide pour le champ #%d", $field->getId()),
                ));
                break;
        }

        return $constraints;
    }
}
