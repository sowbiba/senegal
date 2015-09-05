<?php

namespace Senegal\ApiBundle\Manager;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializerInterface;
use Senegal\ApiBundle\Event\ApiEvents;
use Senegal\ApiBundle\Event\FilterManagerEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class CRUDManager extends AbstractManager
{
    const EVENT_POST_CREATE = ApiEvents::POST_MANAGER_CREATE;
    const EVENT_PRE_INSERT = ApiEvents::PRE_MANAGER_INSERT;
    const EVENT_PRE_CRUD_UPDATE = ApiEvents::PRE_MANAGER_CRUD_UPDATE;
    const EVENT_PRE_UPDATE = ApiEvents::PRE_MANAGER_UPDATE;
    const EVENT_POST_INSERT_SAVE = ApiEvents::POST_MANAGER_INSERT_SAVE;
    const EVENT_POST_UPDATE_SAVE = ApiEvents::POST_MANAGER_UPDATE_SAVE;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected $entityClass;

    protected $eventNameRoot;

    /**
     * @param EntityManager            $em
     * @param $entityClass
     * @param ValidatorInterface       $validator
     * @param SerializerInterface      $serializer
     * @param EventSubscriberInterface $eventSubscriber
     */
    public function __construct(
        EntityManager $em,
        $entityClass,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EventSubscriberInterface $eventSubscriber = null
    ) {
        if (null !== $eventSubscriber) {
            $this->dispatcher = new EventDispatcher();
            $this->dispatcher->addSubscriber($eventSubscriber);
        }

        $this->validator            = $validator;
        $this->serializer           = $serializer;
        $this->entityClass = $entityClass;

        parent::__construct($em, $entityClass);
    }

    /**
     * Create new entity.
     *
     * @return mixed
     */
    public function create()
    {
        $entity = new $this->entityClass();

        if (null !== $this->dispatcher) {
            $event = $this->dispatcher->dispatch(static::EVENT_POST_CREATE, new FilterManagerEvent($entity));
            $entity = $event->getEntity();
        }

        return $entity;
    }

    public function insert(array $data = [], $validationConstraints = null, $validationGroups = null)
    {
        if (null !== $this->dispatcher) {
            $event = $this->dispatcher->dispatch(static::EVENT_PRE_INSERT, new FilterManagerEvent(null, $data));
            $data = $event->getData();
        }

        $this->cleanData($data);

        $entity = $this->create();
        $this->hydrateObject($entity, $data);

        $errorList = $this->validate($entity, $validationConstraints, $validationGroups);
        if (count($errorList)) {
            throw new UnprocessableEntityHttpException($this->serializer->serialize($errorList, 'json'));
        }

        return $this->insertSave($entity);
    }

    protected function insertSave($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(static::EVENT_POST_INSERT_SAVE, new FilterManagerEvent($entity));
        }

        return $entity;
    }

    public function update($entity, array $data = [], $validationConstraints = null, $validationGroups = null)
    {
        if (null !== $this->dispatcher) {
            $event = $this->dispatcher->dispatch(static::EVENT_PRE_CRUD_UPDATE, new FilterManagerEvent($entity, $data));
            $data = $event->getData();

            $event = $this->dispatcher->dispatch(static::EVENT_PRE_UPDATE, new FilterManagerEvent($entity, $data));
            $data = $event->getData();
        }

        $this->cleanData($data);

        $this->hydrateObject($entity, $data);

        $errorList = $this->validate($entity, $validationConstraints, $validationGroups);
        if (count($errorList)) {
            throw new UnprocessableEntityHttpException($this->serializer->serialize($errorList, 'json'));
        }

        return $this->updateSave($entity);
    }

    protected function updateSave($entity)
    {
        $this->em->merge($entity);
        $this->em->flush();

        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(static::EVENT_POST_UPDATE_SAVE, new FilterManagerEvent($entity));
        }

        return $entity;
    }

    /**
     * Delete entity.
     *
     * @param $entity
     *
     * @return bool
     */
    public function delete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * Validate the entity using \Symfony\Component\Validator\Constraints.
     *
     * @param $entity
     * @param null $constraints
     * @param null $groups
     *
     * @return ConstraintViolationListInterface
     */
    public function validate($entity, $constraints = null, $groups = null)
    {
        return $this->validator->validate($entity, $constraints, $groups);
    }

    /**
     * Unset all null data.
     *
     * @param $data
     */
    final protected function cleanData(&$data)
    {
        foreach ($data as $key => $d) {
            if (null === $d) {
                unset($data[$key]);
            }
        }
    }

    /**
     * Set object attributes.
     *
     * Example :
     * $data = [
     *      'id' => 3
     *      'isActive' => true
     * ]
     *
     * Will do $obj->setId(3) and $obj->setIsActive(true)
     *
     * @param $obj
     * @param $data
     */
    final protected function hydrateObject($obj, $data)
    {
        foreach ($data as $key => $value) {
            $setMethodName = 'set'.ucfirst($key);

            if (method_exists($obj, $setMethodName)) {
                call_user_func([$obj, $setMethodName], $value);
            }
        }
    }
}
