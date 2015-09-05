<?php

namespace Pfd\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IdentityType extends AbstractType
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
        $productLineList = $this->client->get('product-lines',
            [
                'query' => [
                        'active' => 1,
                        'serializerGroups' => ['contract_set_identity_create', 'contract_set_identity_update'],
                        'sortField' => 'name',
                        'sortOrder' => 'asc',
                    ],
            ]
        )->json();

        $groupList = $this->client->get('groups',
            [
                'query' => [
                        'serializerGroups' => ['contract_set_identity_create', 'contract_set_identity_update'],
                        'sortField' => 'name',
                        'sortOrder' => 'asc',
                    ],
            ]
        )->json();
        $groupList = $groupList['groups'];

        $builder
            ->add('active', 'checkbox',
                [
                    'label' => 'Actif',
                    'required' => false,
                ]
            )
            ->add('productLine', 'choice',
                [
                    'label' => 'Gamme',
                    'required' => true,
                    'choices' => empty($productLineList) ? [] : array_reduce($productLineList, function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                ]
            )
            ->add('clientName', 'text',
                [
                    'label' => "Nom du point d'entrée sur le front",
                    'required' => true,
                ]
            )
            ->add('profideoName', 'text',
                [
                    'label' => "Nom du point d'entrée sur le back",
                    'required' => true,
                ]
            )
            ->add('groups', 'choice',
                [
                    'label' => 'Accessibilité',
                    'required' => false,
                    'choices' => empty($groupList) ? [] : array_reduce($groupList, function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                    'multiple' => true,
                ]
            )
            ->add('releaseName', 'text',
                [
                    'label' => 'Nom de la version',
                    'required' => true,
                ]
            )
            ->add('contractsLastUpdateDateShown', 'checkbox',
                [
                    'label' => 'Afficher la date de dernière maJ de contrats',
                    'required' => false,
                ]
            )
            ->add('frontHomeDataTooltipsShown', 'checkbox',
                [
                    'label' => 'Afficher les infobulles sur les données maisons FRONT',
                    'required' => false,
                ]
            )
            ->add('matchPageInsurersShown', 'checkbox',
                [
                    'label' => 'Afficher les assureurs sur la page des matchs',
                    'required' => false,
                ]
            )
            ->add('matchPageContractsMarketingStateShown', 'checkbox',
                [
                    'label' => "Afficher l'état de commercialisation des contrats sur la page des matchs",
                    'required' => false,
                ]
            )
            ->add('matchPageDocumentsShown', 'checkbox',
                [
                    'label' => 'Afficher les documents sur la page des matchs',
                    'required' => false,
                ]
            )
            ->add('concurrencyVersion', 'hidden',
                [
                    'label'    => 'form_labels.concurrencyVersion',
                    'required' => false,

                ]
            )
        ;
    }

    public function getName()
    {
        return 'identity';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'translation_domain' => 'back_contract_set',
        ]);
    }
}
