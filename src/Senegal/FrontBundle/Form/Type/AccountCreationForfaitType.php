<?php

namespace Senegal\FrontBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AccountCreationForfaitType
 */
class AccountCreationForfaitType extends AbstractType
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
        $forfaitList = $this->client->get('forfaits',
            [
                'query' => [
                        'environment'       => 'front',
                        'serializerGroup'   => 'user_create',
                        'sortField'         => 'description',
                        'sortOrder'         => 'asc',
                    ],
            ]
        )->json();

        $builder
            ->add('forfait', 'choice',
                [
                    'label'    => 'form_labels.forfait',
                    'required' => true,
                    'choices'  => empty($forfaitList) || !isset($forfaitList['forfaits']) || empty($forfaitList['forfaits']) ? [] : array_reduce($forfaitList['forfaits'], function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                    'empty_value' => 'form_empty_value.forfait',
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'account_creation_forfait';
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
