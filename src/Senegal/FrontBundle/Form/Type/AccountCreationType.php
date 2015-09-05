<?php

namespace Senegal\FrontBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AccountCreationType
 */
class AccountCreationType extends AbstractType
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
//        $roleList = $this->client->get('roles',
//            [
//                'query' => [
//                        'serializerGroups' => ['user_create', 'user_update'],
//                        'sortField' => 'description',
//                        'sortOrder' => 'asc',
//                    ],
//            ]
//        )->json();

        $builder
            ->add('identification', new AccountCreationIdentificationType())
            ->add('forfait', new AccountCreationForfaitType($this->client))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'account_creation';
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
