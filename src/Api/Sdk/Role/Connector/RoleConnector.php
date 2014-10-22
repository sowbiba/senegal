<?php

namespace Api\Sdk\Role\Connector;

use Api\Sdk\Connector\AbstractConnector;
use Api\Sdk\Model\Role;
use Api\Sdk\Role\Query\RoleEspriQuery;

/**
 * Class RoleConnector
 */
class RoleConnector extends AbstractConnector implements RoleConnectorInterface
{
    /**
     * Retrieve role by name (ex : backoffice_user)
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getByName($name)
    {
        return $this->getConnectorToUse("getByName")->getByName($name);
    }

}
