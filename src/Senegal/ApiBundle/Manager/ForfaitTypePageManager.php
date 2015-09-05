<?php

namespace Senegal\ApiBundle\Manager;

use Senegal\ApiBundle\Entity\ForfaitHasTypePage;
use Senegal\ApiBundle\Event\ApiEvents;
use Senegal\ApiBundle\Utils\ArrayDiff;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ForfaitTypePageManager extends CRUDManager
{
//    const EVENT_PRE_INSERT = ApiEvents::PRE_MANAGER_FORFAIT_INSERT;
//    const EVENT_PRE_UPDATE = ApiEvents::PRE_MANAGER_FORFAIT_UPDATE;

    /**
     * @param array        $filters
     *
     * @return ForfaitHasTypePage
     */
    public function findByFilters(array $filters = [])
    {
        return $this->repository->findByFilters($filters);
    }
}
