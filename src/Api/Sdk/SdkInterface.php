<?php
/**
 * Interface that each Sdk must implements.
 * It defines basic methods that are useful in each of these Sdk.
 *
 * In all the following comments, the word "object" refers to the Sdk that implements this method.
 * For example, in ContractSdk, we'll have a collection of Contracts.
 *
 * Author: Florent Coquel
 * Date: 19/09/13
 */
namespace Api\Sdk;

use Api\Sdk\Connector\ConnectorInterface;
use Api\Sdk\Mediator\ColleagueInterface;
use Api\Sdk\Query\QueryInterface;

interface SdkInterface extends ColleagueInterface
{
    public function __construct(ConnectorInterface $connector);

    /**
     * Returns the count of objects matching the given query
     *
     * @param \Api\Sdk\Query\QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query);

    /**
     * Returns the collection of all exisiting objects
     *
     * @return array
     */
    public function getAll();

    /**
     * Returns the object matching the given id
     *
     * @param type $id
     */
    public function getById($id);

    /**
     * Returns the collection of objects matching the given query
     *
     * @param \Api\Sdk\Query\QueryInterface $query
     *
     * @return array
     */
    public function getCollection(QueryInterface $query);

    /**
     * Return true if the SDK manage the entity class
     *
     * @param $classname
     *
     * @return bool
     */
    public function supports($classname);
}
