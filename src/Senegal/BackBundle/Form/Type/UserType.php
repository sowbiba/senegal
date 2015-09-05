<?php

namespace Senegal\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roleList = $this->client->get('roles',
            [
                'query' => [
                        'serializerGroups' => ['user_create', 'user_update'],
                        'sortField' => 'description',
                        'sortOrder' => 'asc',
                    ],
            ]
        )->json();

        $builder
            ->add('email', 'email',
                [
                    'label'    => 'form_labels.mail',
                    'required' => false,
                ]
            )
            ->add('username', 'text',
                [
                    'label'    => 'form_labels.username',
                    'required' => true,
                ]
            )
            ->add('password', 'password',
                [
                    'label'    => 'form_labels.password',
                    'required' => false,
                ]
            )
            ->add('active', 'checkbox',
                [
                    'label'    => 'form_labels.active',
                    'required' => false,
                ]
            )
            ->add('role', 'choice',
                [
                    'label'    => 'form_labels.role',
                    'required' => true,
                    'choices'  => empty($roleList) ? [] : array_reduce($roleList, function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                    'empty_value' => 'form_empty_value.role',
                ]
            )
            ->add('lastname', 'text',
                [
                    'label'    => 'form_labels.lastname',
                    'required' => false,
                ]
            )
            ->add('firstname', 'text',
                [
                    'label'    => 'form_labels.firstname',
                    'required' => false,
                ]
            )
            ->add('phone', 'text',
                [
                    'label'    => 'form_labels.phone',
                    'required' => false,
                ]
            )
            ->add('address', 'textarea',
                [
                    'label'    => 'form_labels.address',
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
        return 'user';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'translation_domain' => 'back_users',
        ));
    }
}
