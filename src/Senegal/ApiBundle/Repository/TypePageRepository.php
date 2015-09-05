<?php

namespace Senegal\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Senegal\ApiBundle\Entity\TypePage;
use Senegal\ApiBundle\Utils\DateConverter;;

class TypePageRepository extends EntityRepository
{
    /**
     * Find all typePages by filters.
     *
     * @param array   $filters
     * @param string  $sortField
     * @param string  $sortOrder
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     */
    public function findByFilters(array $filters = [], $sortField, $sortOrder, $limit, $offset)
    {
        $fieldsConverter = [
            'createdAt' => 'f.createdAt',
            'name' => 'f.name',
        ];

        $query = $this->createQueryBuilder('f')
        ;

        $i = 0;
        foreach ($filters as $fieldSlug => $value) {
            if (isset($fieldsConverter[$fieldSlug])) {
                $field = $fieldsConverter[$fieldSlug];

                if ('createdAt' === $fieldSlug) {
                    $date = DateConverter::convertDateToDatetime($value);

                    if (null !== $date) {
                        $date = $date->format('Y-m-d');
                        $startValue = $date.' 00:00:00';
                        $endValue = $date.' 23:59:59';

                        $query = $query
                            ->andWhere($query->expr()->between($field, "'$startValue'", "'$endValue'"));
                    } else {
                        return [
                            'total' => 0,
                            'typePages' => [],
                        ];
                    }
                } else {
                    $query = $query
                        ->andWhere("$field = ?$i")
                        ->setParameter($i, $value)
                    ;
                }

                $i++;
            }
        }

        $query = $query
            ->orderBy((isset($fieldsConverter[$sortField])) ? $fieldsConverter[$sortField] : $fieldsConverter['name'], $sortOrder)
            ->addOrderBy('f.id', $sortOrder)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        $paginator = new Paginator($query);

        return [
            'total' => $paginator->count(),
            'typePages' => $paginator->getQuery()->getResult(),
        ];
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Senegal\ApiBundle\Entity\TypePage' === $class;
    }
}
