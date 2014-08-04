<?php

namespace Api\Sdk\Role\Connector\Doctrine;

use Doctrine\ORM\EntityManager;
use Api\Sdk\Connector\AbstractDoctrineConnector;
use Api\Sdk\Query\QueryInterface;
use Api\SdkBundle\Entity\Role;

/*
 * Class RoleDoctrineConnector
 */

class RoleDoctrineConnector extends AbstractDoctrineConnector
{
    /**
     * Instance a role doctrine connector and set the repository to 'Role'
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);

        $this->setRepository('Role');
    }

    /**
     * @param int $id
     *
     * @return array|null
     */
    public function getById($id)
    {
        return $this->getOne($id);
    }

    /**
     * Returns the roles matching the given query
     *
     * @param Api\Sdk\Query\QueryInterface $query
     *
     * @return array Role entities
     */
    public function getCollection(QueryInterface $query)
    {
        $roles = $this->getResult($query);

        return array_map(function ($role) {
            return $this->convert($role);
        }, $roles);
    }

    /**
     * Retrieves a role by name
     *
     * @param      $name
     * @param bool $convert
     *
     * @return array
     * @throws \BadMethodCallException
     */
    public function getByName($name)
    {
        if (null === $name) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($name) . ' given');
        }

        $role = $this->repository->findOneBy(array('name' => $name));

        if (!$role) {
            return false;
        }

        return $this->convert($role);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function create(array $data)
    {

        $role = new Role();
        $role->populate($data);

        try {
            $this->em->persist($role);
            $this->em->flush();
        } catch (\PDOException $e) {
            $this->getLogger()->error($e->getMessage());
        }

        return $this->convert($role);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function updateByName(array $data)
    {
        try {
            $role = $this->em->getRepository('ApiSdkBundle:Role')->findOneByName( $data['name'] );
            $role->setDescription( $data['description'] );

            $this->em->flush();
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }

        return $this->convert($role);
    }

    public function delete( array $role){
        try {
            $entity = $this->em->getRepository('ApiSdkBundle:Role')->findOneByName( $role['name'] );
            $this->em->remove($entity);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }

        return true;
    }
}
