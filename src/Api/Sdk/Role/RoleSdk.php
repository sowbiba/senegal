<?php
namespace Api\Sdk\Role;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Model\Role;
use Api\Sdk\SdkInterface;
use Api\Sdk\Query\QueryInterface;

/**
 *
 * Class RoleSdk
 */
class RoleSdk extends AbstractSdk implements SdkInterface
{
    /**
     * @param \Api\Sdk\Query\QueryInterface $query
     *
     * @return Api\Sdk\Model\Role[]
     */
    public function getCollection(QueryInterface $query)
    {
        $rolesData = $this->connector->getCollection($query);

        $roles = array_map(function ($role) {
            return new Role($this, $role);
        }, $rolesData);

        return $roles;
    }

    /**
     * Return all roles
     *
     * @return Api\Sdk\Model\Role[]
     */
    public function getAll()
    {
        $roles = $this->connector->getAll();

        return array_map(function ($role) {
            return new Role($this, $role);
        },$roles);
    }
    /**
     * @param string $classname
     *
     * @return bool
     */
    public function supports($classname)
    {
        return $classname === 'Api\Sdk\Model\Role';
    }
}
