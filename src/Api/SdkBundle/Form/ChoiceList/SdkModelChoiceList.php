<?php

namespace Api\SdkBundle\Form\ChoiceList;

use Api\Sdk\Mediator\SdkMediator;
use Api\Sdk\Model\BaseModel;
use Symfony\Bridge\Propel1\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Widely inspired by the EntityChoiceList.
 */
class SdkModelChoiceList extends ObjectChoiceList
{
    /**
     * Constructor.
     *
     * @see ModelType How to use the preferred choices.
     *
     * @param SdkMediator $mediator  The SDK manager
     * @param string      $class     The FQCN of the model class to be loaded.
     * @param string      $labelPath A property path pointing to the property used for the choice labels.
     * @param array       $choices   An optional array to use, rather than fetching the models.
     * @param string      $groupPath A property path pointing to the property used to group the choices.
     * @param string      $orderBy   A property path pointing to the property used to sort the choices.
     * @param array       $preferred The preferred items of this choice.
     *
     * @param PropertyAccessorInterface $propertyAccessor The reflection graph for reading property paths.
     */
    public function __construct(SdkMediator $mediator, $class, $labelPath = null, $choices = null, $groupPath = null, $orderBy = null, $preferred = array(), PropertyAccessorInterface $propertyAccessor = null)
    {
        if (null === $choices) {
            $choices = $mediator->getSdkByClass($class)->getAll();
        }

        if (null !== $orderBy) {
            $choices = $this->orderChoices($choices, $orderBy);
        }

        parent::__construct($choices, $labelPath, $preferred, $groupPath, null, $propertyAccessor);
    }

    /**
     * @param array  $choices
     * @param string $orderBy
     *
     * @return array
     */
    protected function orderChoices(array $choices, $orderBy)
    {
        $final = array();
        $method = 'get'.ucfirst($orderBy);

        foreach ($choices as $choice) {
            $final[strtolower($choice->$method())] = $choice;
        }

        ksort($final);

        return $final;
    }

    /**
     * Creates a new unique value for this choice.
     *
     * If a property path for the value was given at object creation,
     * the getter behind that path is now called to obtain a new value.
     * Otherwise a new integer is generated.
     *
     * @param mixed $choice The choice to create a value for
     *
     * @return integer|string A unique value without character limitations.
     */
    protected function createValue($entity)
    {
        return $this->fixValue($entity->getId());
    }

    /**
     * Creates a new unique index for this entity.
     *
     * If the entity has a single-field identifier, this identifier is used.
     *
     * Otherwise a new integer is generated.
     *
     * @param mixed $entity The choice to create an index for
     *
     * @return integer|string A unique index containing only ASCII letters,
     *                        digits and underscores.
     */
    protected function createIndex($entity)
    {
        return $this->fixIndex($entity->getId());
    }

    /**
     * Returns the values corresponding to the given model objects.
     *
     * @param array $models
     *
     * @return array
     *
     * @see ChoiceListInterface
     */
    public function getValuesForChoices(array $models)
    {
        $values = array();
        foreach ($models as $model) {
            if ($model instanceof BaseModel) {
                $values[] = $this->fixValue($model->getId());
            }
        }

        return $values;
    }
}
