<?php

namespace Senegal\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Senegal\ApiBundle\Entity\User;
use Senegal\ApiBundle\Utils\DateConverter;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * @param string $username
     *
     * @return User
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->andWhere('u.active = 1')
            ->setParameter('username', $username)
            ->join('u.role', 'r')
            ->addSelect('r')
            ->getQuery()
        ;

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Find all users by filters.
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
            'active' => 'u.active',
            'email' => 'u.email',
            'firstname' => 'u.firstname',
            'lastname' => 'u.lastname',
            'role' => 'r.name',
            'roleId' => 'r.id',
            'createdAt' => 'u.createdAt',
            'username' => 'u.username',
        ];

        $query = $this->createQueryBuilder('u')
            ->leftjoin('u.role', 'r')
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
                            'users' => [],
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
            ->orderBy((isset($fieldsConverter[$sortField])) ? $fieldsConverter[$sortField] : $fieldsConverter['username'], $sortOrder)
            ->addOrderBy('u.id', $sortOrder)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        $paginator = new Paginator($query);

        return [
            'total' => $paginator->count(),
            'users' => $paginator->getQuery()->getResult(),
        ];
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Senegal\ApiBundle\Entity\User' === $class;
    }

    /**
     * @param UserInterface $user
     *
     * @return mixed|UserInterface
     *
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $refreshedUser = $this->loadUserByUsername($user->getUsername());

        return $refreshedUser;
    }
}
