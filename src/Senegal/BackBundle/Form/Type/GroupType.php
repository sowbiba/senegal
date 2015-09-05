<?php

namespace Pfd\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupType extends AbstractType
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
        $contractSetList = $this->client->get('contract-set/identities',
            [
                'query' => [
                        'zone' => 'publish',
                        'serializerGroups' => ['group_create', 'group_update'],
                        'sortField' => 'profideoName',
                        'sortOrder' => 'desc',
                    ],
            ]
        )->json();
        $contractSetList = $contractSetList['contractSetIdentities'];

        $builder
            ->add('name', 'text',
                [
                    'label'    => 'form_labels.name',
                    'required' => true,
                ]
            )
            ->add('contractSetIdentities', 'choice',
                [
                    'label'    => 'form_labels.contract_sets',
                    'required' => false,
                    'choices'  => empty($contractSetList) ? [] : array_reduce($contractSetList, function ($result, $item) {
                        $result[$item['id']] = $item['profideoName'];

                        return $result;
                    }, []),
                    'multiple' => true,
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'group';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'translation_domain' => 'back_groups',
        ]);
    }
}
