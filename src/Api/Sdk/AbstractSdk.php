<?php
/**
 * You MUST go through it if you want to retrieve objects (contract, product line, ...)
 *
 * This class lists all the high-level methods.
 * This class can only use POPO classes (\Api\Sdk\Model)
 * To use this class you have to initialize a connector (\Api\Sdk\Connector) and pass it to the constructor
 * These connectors work with POPO objects, to save an object you have to pass it
 * Only connectors can use entities (\Api\SdkBundle\Entity)
 *
 * Author: Florent Coquel
 * Date: 17/09/13
 **/
namespace Api\Sdk;

use Api\Sdk\Connector\ConnectorInterface;
use Api\Sdk\Connector\NotImplementedException;
use Api\Sdk\Event\Events;
use Api\Sdk\Event\ModelEvent;
use Api\Sdk\Model\BaseModel;
use Api\Sdk\Mediator\MediatorInterface;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\SdkException;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractSdk
{
    /** @var ConnectorInterface $connector */
    protected $connector;

    /** @var MediatorInterface $manager */
    protected $mediator;

    /** @var  EventDispatcherInterface */
    protected $dispatcher;

    /**
     * Creates a new instance of sdk setting up the context needed
     * to retrieve and manage all POPO objects from the Profideo API.
     *
     * You must provide an implementation of ConnectorInterface
     * when instantiating this object in order to  inject the service which
     * connects the high-level API to the data-source, the model and other
     * technical implementations (like the legacy bridge).
     *
     * See also DataConnector, PropelConnector, DoctrineConncector
     * and the ConnectorInterface source code to know more.
     *
     * @param ConnectorInterface $connector
     *
     * @internal param $ConnectorInterface
     */
    public function __construct(ConnectorInterface $connector, EventDispatcherInterface $dispatcher = null)
    {
        $this->connector  = $connector;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param QueryInterface $query
     *
     * @return int|void
     * @throws Connector\NotImplementedException
     *
     * we need this method for smooth migration,  it will disappear
     * @codeCoverageIgnore
     */
    public function count(QueryInterface $query)
    {
        throw new NotImplementedException("Method count() in your SDK class");
    }

    /**
     * @throws Connector\NotImplementedException
     *
     * we need this method for smooth migration,  it will disappear
     * @codeCoverageIgnore
     */
    public function getAll()
    {
        throw new NotImplementedException("Method getAll() in your SDK class");
    }

    /**
     * @param $id
     *
     * @throws Connector\NotImplementedException
     *
     * we need this method for smooth migration,  it will disappear
     * @codeCoverageIgnore
     */
    public function getById($id)
    {
        throw new NotImplementedException("Method getById() in your SDK class");
    }

    /**
     * @param QueryInterface $query
     *
     * @return array|void
     * @throws Connector\NotImplementedException
     *
     * we need this method for smooth migration,  it will disappear
     * @codeCoverageIgnore
     */
    public function getCollection(QueryInterface $query)
    {
        throw new NotImplementedException("Method getCollection() in your SDK class");
    }

    /**
     * @param QueryInterface $query
     * @param array          $sorts
     *
     * @return QueryInterface|null
     */
    public function getSortQuery(QueryInterface $query, array $sorts = array())
    {
        return null;
    }

    /**
     * @param MediatorInterface $mediator
     */
    public function setMediator(MediatorInterface $mediator)
    {
        $this->mediator = $mediator;
    }

    /**
     * @param mixed $data
     */
    public function create($data)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(Events::PRE_CREATE, new ModelEvent($this, $data));
        }

        $result = $this->doCreate($data);

        if ($result && null !== $this->dispatcher) {
            $this->dispatcher->dispatch(Events::POST_CREATE, new ModelEvent($this, $result));
        }

        return $result;
    }

    /**
     * @return boolean
     */
    protected function doCreate($data)
    {
        throw new NotImplementedException("Method doCreate in your SDK class");
    }

    /**
     * @param mixed $data
     */
    public function update($data)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(Events::PRE_UPDATE, new ModelEvent($this, $data));
        }

        $result = $this->doUpdate($data);

        if ($result && null !== $this->dispatcher) {
            $this->dispatcher->dispatch(Events::POST_UPDATE, new ModelEvent($this, $result));
        }

        return $result;
    }

    /**
     * @return boolean
     */
    protected function doUpdate($data)
    {
        throw new NotImplementedException("Method doUpdate in your SDK class");
    }

    /**
     * @param BaseModel $object
     */
    public function delete(BaseModel $object)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(Events::PRE_DELETE, new ModelEvent($this, $object));
        }

        $result = $this->doDelete($object);

        if ($result && null !== $this->dispatcher) {
            $this->dispatcher->dispatch(Events::POST_DELETE, new ModelEvent($this, $object));
        }

        return $result;
    }

    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @param BaseModel $object
     *
     * @return boolean
     */
    protected function doDelete(BaseModel $object)
    {
        throw new NotImplementedException("Method doDelete in your SDK class");
    }
    public function getMediator()
    {
        return $this->mediator;
    }

    /**
     * Returns a sdk exception
     *
     * @param string $message
     *
     * @return \Api\Sdk\SdkException
     */
    protected function createSdkException($message)
    {
        return new SdkException($message);
    }
}
