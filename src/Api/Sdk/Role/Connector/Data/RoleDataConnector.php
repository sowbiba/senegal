<?php

namespace Api\Sdk\Role\Connector\Data;

use Api\Sdk\Connector\AbstractDataConnector;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\Role\Query\RoleQuery;

/**
 * Class RoleDataConnector
 */
class RoleDataConnector extends AbstractDataConnector
{
    /**
     * Return collection of roles
     *
     * @param \Api\Sdk\Role\Connector\Data\QueryInterface $query
     *
     * @return array
     */
    public function getCollection(QueryInterface $query)
    {
        return $this->getDatas('role', $query);
    }

    /**
     * Return all roles
     *
     * @return Api\Sdk\Model\Role[]
     */
    public function getAll()
    {
        return $this->getDatas('role');
    }

    public function getByName($name)
    {
        $query = new RoleQuery(array("name"=>$name));

        return $this->getDatas('role', $query);
    }
}
