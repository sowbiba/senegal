<?php

namespace Api\SdkBundle\Listener;

use Api\Sdk\Event\ModelEvent;
use Api\Sdk\Model\BlameableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BlameableListener
 */
class BlameableListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        // We cannot inject the security context directly due to circular reference so we have to inject the container
        $this->container = $container;
    }

    /**
     * @param ModelEvent $args
     */
    public function preCreate(ModelEvent $args)
    {
        $object = $args->getData();

        if (!$object instanceof BlameableInterface || null === ($user = $this->getUser())) {
            return;
        }

        $object->setCreatedBy($user);
        $object->setUpdatedBy($user);
    }

    /**
     * @param ModelEvent $args
     */
    public function preUpdate(ModelEvent $args)
    {
        $object = $args->getData();

        if (!$object instanceof BlameableInterface || null === ($user = $this->getUser())) {
            return;
        }

        $object->setUpdatedBy($user);
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser()
    {
        $token = $this->container->get('security.context')->getToken();

        if ($token === null || !($user = $token->getUser()) instanceof UserInterface) {
            return;
        }

        return $user;
    }
}
