<?php

namespace Senegal\Api\SdkBundle\Form\Type;

use Pfd\Sdk\Mediator\SdkMediator;
use Senegal\Api\SdkBundle\Form\ChoiceList\SdkModelChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * SdkModelType class.
 */
class SdkModelType extends AbstractType
{
    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * @var SdkMediator
     */
    protected $manager;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(SdkMediator $mediator, PropertyAccessorInterface $propertyAccessor)
    {
        $this->manager = $mediator;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $propertyAccessor = $this->propertyAccessor;
        $manager = $this->manager;

        $choiceList = function (Options $options) use ($manager, $propertyAccessor) {
            return new SdkModelChoiceList(
                $manager,
                $options['class'],
                $options['property'],
                $options['choices'],
                $options['group_by'],
                $options['order_by'],
                $options['preferred_choices'],
                $propertyAccessor
            );
        };

        $resolver->setDefaults(array(
            'template'          => 'choice',
            'multiple'          => false,
            'expanded'          => false,
            'class'             => null,
            'property'          => null,
            'query'             => null,
            'choices'           => null,
            'choice_list'       => $choiceList,
            'group_by'          => null,
            'order_by'          => null,
            'by_reference'      => false,
        ));
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sdk_model';
    }
}
