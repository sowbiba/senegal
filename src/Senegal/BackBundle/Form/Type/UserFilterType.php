<?php

namespace Senegal\BackBundle\Form\Type;

use GuzzleHttp\Client;
use Senegal\SecurityBundle\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserFilterType extends AbstractType
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var TokenStorageInterface
     */
    private $token;

    /**
     * @param Client $client
     */
    public function __construct(Client $client, TokenStorageInterface $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roleList = $this->client->get('roles',
            [
                'body' => [
                        'fields' => 'id, name, description',
                        'orderBy' => 'description asc',
                    ],
                'headers' => ['api-key' => $this->getCurrentUser()->getToken()],
            ]
        )->json();

        $roleList = !isset($roleList['_embedded']['roles']) || empty($roleList['_embedded']['roles']) ? [] : $roleList['_embedded']['roles'];

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

    /**
     * @return User
     */
    private function getCurrentUser()
    {
        return $this->token->getToken()->getUser();
    }
}
