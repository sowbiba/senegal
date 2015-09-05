<?php

namespace Pfd\BackBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RapprochementSetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden',
                [
                    'label'    => false,
                    'required' => true,
                ]
            )
            ->add('totalContracts', 'hidden',
                [
                    'label'    => false,
                    'required' => true,
                    'property_path' => '[contracts][totalContracts]',
                    'disabled' => true,
                ]
            )
            ->add('title', 'text',
                [
                    'label'    => false,
                    'required' => true,
                ]
            )
            ->add('workingFormula', 'textarea',
                [
                    'label'    => false,
                    'required' => false,
                ]
            )
            ->add('position', 'hidden',
                [
                    'label'    => false,
                    'required' => true,
                ]
            )
            ->add('isWorkingFormulaValid', 'hidden',
                [
                    'label'    => false,
                    'required' => true,
                    'disabled' => true,
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rapprochement_set';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
