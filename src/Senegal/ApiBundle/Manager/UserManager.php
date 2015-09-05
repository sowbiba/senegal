<?php

namespace Senegal\ApiBundle\Manager;

use Senegal\ApiBundle\Entity\User;
use Senegal\ApiBundle\Event\ApiEvents;
use Senegal\ApiBundle\Utils\ArrayDiff;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserManager extends CRUDManager
{
    const EVENT_PRE_INSERT = ApiEvents::PRE_MANAGER_USER_INSERT;
    const EVENT_PRE_UPDATE = ApiEvents::PRE_MANAGER_USER_UPDATE;

    /**
     * @param $username
     *
     * @return User
     */
    public function loadUserByUsername($username)
    {
        return $this->repository->loadUserByUsername($username);
    }

    /**
     * @param array        $filters
     * @param string       $sortField
     * @param string       $sortOrder
     * @param null|integer $limit
     * @param null|integer $offset
     *
     * @return array
     */
    public function findByFilters(array $filters = [], $sortField, $sortOrder, $limit, $offset)
    {
        return $this->repository->findByFilters($filters, $sortField, $sortOrder, $limit, $offset);
    }
}
