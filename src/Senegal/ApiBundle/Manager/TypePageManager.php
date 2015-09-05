<?php

namespace Senegal\ApiBundle\Manager;

use Senegal\ApiBundle\Entity\TypePage;
use Senegal\ApiBundle\Event\ApiEvents;
use Senegal\ApiBundle\Utils\ArrayDiff;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TypePageManager extends CRUDManager
{
    const EVENT_PRE_INSERT = ApiEvents::PRE_MANAGER_FORFAIT_INSERT;
    const EVENT_PRE_UPDATE = ApiEvents::PRE_MANAGER_FORFAIT_UPDATE;

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
