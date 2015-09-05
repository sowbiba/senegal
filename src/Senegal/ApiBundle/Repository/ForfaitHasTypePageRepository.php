<?php

namespace Senegal\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Senegal\ApiBundle\Entity\ForfaitHasTypePage;
use Senegal\ApiBundle\Utils\DateConverter;;

class ForfaitHasTypePageRepository extends EntityRepository
{
    /**
     * Find a forfaitHasTypePage by filters.
     *
     * @param array   $filters
     *
     * @return ForfaitHasTypePage
     */
    public function findByFilters(array $filters = [])
    {
        $fieldsConverter = [
            'forfait' => 'ft.forfait',
            'typePage' => 'ft.typePage',
        ];

        $query = $this->createQueryBuilder('ft')
        ;

        $i = 0;
        foreach ($filters as $fieldSlug => $value) {
            if (isset($fieldsConverter[$fieldSlug])) {
                $field = $fieldsConverter[$fieldSlug];

                $query = $query
                    ->andWhere("$field = ?$i")
                    ->setParameter($i, $value)
                ;

                $i++;
            }
        }

        return $query
            ->orderBy('ft.typePage', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Senegal\ApiBundle\Entity\ForfaitHasTypePage' === $class;
    }
}
