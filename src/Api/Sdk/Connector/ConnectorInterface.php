<?php
namespace Api\Sdk\Connector;

use Api\Sdk\Mediator\ColleagueInterface;
use Api\Sdk\Query\QueryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ConnectorInterface
 * @package Api\Sdk\Connector
 * @author  Florent Coquel
 * @since   20/05/13
 */
interface ConnectorInterface extends ColleagueInterface
{
    public function setLogger(LoggerInterface $logger);

    /**
     * Returns the count of objects matching the given query
     *
     * @param QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query);

    /**
     * Returns the object matching the given id
     *
     * @param type $id
     */
    public function getById($id);

    /**
     * Returns the collection of objects matching the given query
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function getCollection(QueryInterface $query);

    /**
     * Returns the entire collection of objects
     *
     * @return array
     */
    public function getAll();

}
