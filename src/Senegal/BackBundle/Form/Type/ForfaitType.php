<?php

namespace Senegal\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Senegal\ApiBundle\Entity\ForfaitHasTypePage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ForfaitType
 */
class ForfaitType extends AbstractType
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
        $typePagesList = $this->client->get('type-pages',
            [
                'query' => [
                    'serializerGroups' => ['forfait_create', 'forfait_update'],
                    'sortField' => 'name',
                    'sortOrder' => 'asc',
                ],
            ]
        )->json();

        $builder
            ->add('name', 'text',
                [
                    'label'    => 'form_labels.name',
                    'required' => true,
                ]
            )
            ->add('forfaitTypePages', 'collection', [
                'type' => new ForfaitHasTypePageType($this->client),
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false
            ])
            ->add('typePagesAvailable', 'hidden', ['label' => false, 'required' => false, 'data' => $typePagesList['total'] . " modèles de page au total."])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'forfait';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'translation_domain' => 'back_forfaits',
        ));
    }
}
