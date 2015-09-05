<?php

namespace Senegal\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFilterType extends AbstractType
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roleList = $this->client->get('roles',
            [
                'query' => [
                        'environment' => 'back',
                        'serializerGroup' => 'user_filter',
                        'sortField' => 'description',
                        'sortOrder' => 'asc',
                    ],
            ]
        )->json();

        $builder
            ->setMethod('GET')
            ->add('email', 'text',
                [
                    'label'    => 'form_labels.mail',
                    'required' => false,
                ]
            )
            ->add('username', 'text',
                [
                    'label'    => 'form_labels.username',
                    'required' => false,
                ]
            )
            ->add('active', 'choice',
                [
                    'label'    => 'form_labels.active',
                    'required' => false,
                    'choices'  => [1 => 'Oui', 0 => 'Non'],
                    'empty_value' => 'Choisissez une option',
                ]
            )
            ->add('roleId', 'choice',
                [
                    'label'    => 'form_labels.role',
                    'required' => false,
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
        ;
    }

    public function getName()
    {
        return 'user_filter';
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
