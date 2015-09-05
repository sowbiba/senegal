<?php

namespace Senegal\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ForfaitHasTypePageType
 */
class ForfaitHasTypePageType extends AbstractType
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
            ->add('typePage', 'choice',
                [
                    'required' => true,
                    'choices'  => empty($typePagesList) ? [] : array_reduce($typePagesList['typePages'], function ($result, $item) {
                        $result[$item['id']] = $item['name'];

                        return $result;
                    }, []),
                    'empty_value' => 'form_empty_value.type_page',

                ]
            )
            ->add('allowedPageNumber', 'number')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'type_page';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'translation_domain' => 'back_type_pages',
        ));
    }
}
