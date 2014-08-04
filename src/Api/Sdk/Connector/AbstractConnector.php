<?php
namespace Api\Sdk\Connector;

use Api\Sdk\Mediator\MediatorInterface;
use Api\Sdk\Query\QueryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * This class does almost nothing, except saying that methods are not implemented.
 * Its aim is to be derived, and all its methods to be implemented by its child.
 *
 * For comments on what method does what, please take a look at ConnectorInterface.
 *
 * Class AbstractConnector
 * @package Api\Sdk\Connector
 * @author  Florent Coquel
 * @since   20/05/13
 */
abstract class AbstractConnector implements ConnectorInterface
{
    private $logger;
    private $subConnectors;
    private $mediator;
    private $container;
    protected $validator;

    public function __construct(array $subConnectors)
    {
        $this->subConnectors = $subConnectors;
        $this->validator     = Validation::createValidator();
    }

    /**
     * This method is executed before request
     */
    public function preExecute()
    {
        return true;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return LoggerInterface
     * @throws \ErrorException
     */
    public function getLogger()
    {
        if (!$this->logger instanceof LoggerInterface) {
            throw new \ErrorException("You want to log ? But you don't have define a logger -_-");
        }

        return $this->logger;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @throws NotImplementedException
     */
    public function __call($name, $arguments)
    {
        throw new NotImplementedException($name);
    }

    /**
     * Sometimes we have to use the container because we can't inject the mediator from the DI because it cause
     * a circular reference.
     *
     * @return MediatorInterface
     */
    public function getMediator()
    {
        return null === $this->mediator ? $this->container->get('api.mediator.connector') : $this->mediator;
    }

    /**
     * @param MediatorInterface $mediator
     */
    public function setMediator(MediatorInterface $mediator)
    {
        $this->mediator = $mediator;
        if (count($this->subConnectors) > 0) {
            foreach ($this->subConnectors as $subConnector) {
                $this->mediator->addColleague($subConnector);
            }
        }
    }

    public function getConnectorToUse($methodName)
    {
        if (count($this->subConnectors) > 0) {
            foreach ($this->subConnectors as $subConnector) {
                if (method_exists($subConnector, $methodName)) {
                    return $subConnector;
                }
            }
        }
        throw new NotImplementedException($methodName);
    }

    /**
     * @inheritdoc
     *
     * @param QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query)
    {
        $this->preExecute();

        return $this->getConnectorToUse("count")->count($query);
    }

    /**
     * @inheritdoc
     *
     * @param int $id
     *
     * @return array|null
     */
    public function getById($id)
    {
        return $this->getConnectorToUse("getById")->getById($id);
    }

    /**
     * @inheritdoc
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function getCollection(QueryInterface $query)
    {
        if ($this->preExecute()) {
            return $this->getConnectorToUse("getCollection")->getCollection($query);
        }
    }

    /**
     * @inheritdoc
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function getAll()
    {
        if ($this->preExecute()) {
            return $this->getConnectorToUse("getAll")->getAll();
        }
    }

}
