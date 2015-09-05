<?php

namespace Pfd\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContractFilterType extends AbstractType
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
        $companyGroupList = $this->client->get('company-groups',
            [
                'query' => [
                        'serializerGroups' => ['company_group_list'],
                        'sortField' => 'name',
                        'sortOrder' => 'asc',
                    ],
            ]
        )->json();
        $companyList = $this->client->get('companies',
            [
                'query' => [
                        'serializerGroups' => ['group_list'],
                        'sortField' => 'name',
                        'sortOrder' => 'asc',
                    ],
            ]
        )->json();

        $statusList = $this->client->get('contract-status',
            [
                'query' => [
                        'serializerGroups' => ['contract_status_list'],
                        'sortField' => 'title',
                        'sortOrder' => 'asc',
                    ],
            ]
        )->json();

        $builder
            ->setMethod('GET')
            ->add('name', 'text',
                [
                    'label'    => 'form_labels.name',
                    'required' => false,
                ]
            )
            ->add('isOnsale', 'choice',
                [
                    'label'    => 'form_labels.isOnsale',
                    'required' => false,
                    'choices'  => [1 => 'Oui', 0 => 'Non'],
                    'empty_value' => 'Choisissez une option',
                ]
            )
            ->add('isActive', 'choice',
                [
                    'label'    => 'form_labels.isActive',
                    'required' => false,
                    'choices'  => [1 => 'Oui', 0 => 'Non'],
                    'empty_value' => 'Choisissez une option',
                ]
            )
            ->add('statusId', 'choice',
                [
                    'label'    => 'form_labels.status',
                    'required' => false,
                    'choices'  => empty($statusList) ? [] : array_reduce($statusList, function ($result, $item) {
                        $result[$item['id']] = $item['title'];

                        return $result;
                    }, []),
                    'empty_value' => 'form_empty_value.status',
                ]
            )
            ->add('distributorGroupId', 'choice',
                [
                    'label'    => 'form_labels.distributorGroup',
                    'required' => false,
                    'choices'  => empty($companyGroupList) ? [] : array_reduce($companyGroupList, function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                    'empty_value' => 'form_empty_value.distributorGroup',
                ]
            )
            ->add('distributorId', 'choice',
                [
                    'label'    => 'form_labels.distributor',
                    'required' => false,
                    'choices'  => empty($companyList) ? [] : array_reduce($companyList, function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                    'empty_value' => 'form_empty_value.distributor',
                ]
            )
            ->add('insurerId', 'choice',
                [
                    'label'    => 'form_labels.insurer',
                    'required' => false,
                    'choices'  => empty($companyList) ? [] : array_reduce($companyList, function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                    'empty_value' => 'form_empty_value.insurer',
                ]
            )
        ;
    }

    public function getName()
    {
        return 'contract_filter';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'translation_domain' => 'back_contracts',
        ));
    }
}
