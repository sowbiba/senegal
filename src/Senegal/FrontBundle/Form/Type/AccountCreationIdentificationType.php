<?php

namespace Senegal\FrontBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AccountCreationIdentificationType
 */
class AccountCreationIdentificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text',
                [
                    'label'    => 'form_labels.username',
                    'required' => true,
                ]
            )
            ->add('password', 'password',
                [
                    'label'    => 'form_labels.password',
                    'required' => true,
                ]
            )
            ->add('email', 'email',
                [
                    'label'    => 'form_labels.email',
                    'required' => true,
                ]
            )
            ->add('firstname', 'text',
                [
                    'label'    => 'form_labels.firstname',
                    'required' => true,
                ]
            )
            ->add('lastname', 'text',
                [
                    'label'    => 'form_labels.lastname',
                    'required' => true,
                ]
            )
            ->add('address', 'textarea',
                [
                    'label'    => 'form_labels.address',
                    'required' => false,
                ]
            )
            ->add('phone', 'text',
                [
                    'label'    => 'form_labels.phone',
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'account_creation_identification';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'translation_domain' => 'front_account',
        ));
    }
}
