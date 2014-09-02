<?php

namespace Api\Sdk\User\Connector\Doctrine;

use Api\Sdk\Connector\AbstractDoctrineConnector;
use Doctrine\ORM\EntityManager;
use Api\Sdk\Model\User;
use Api\SdkBundle\Entity\User as UserEntity;
use Api\Sdk\Role\Query\RoleQuery;
use Api\Sdk\User\Query\UserQuery;
use Api\Sdk\Query\QueryInterface;

class UserDoctrineConnector extends AbstractDoctrineConnector
{
    /**
     *
     * @param \Doctrine\ORM\EntityManager $em Doctrine entity manager
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);

        $this->setRepository('User');
    }

    /**
     * Gets the users matching the given query
     *
     * @param QueryInterface $query
     *
     * @return array|User
     */
    public function getCollection(QueryInterface $query)
    {
        $usersEntity = $this->getResult($query);

        return array_map(function ($userEntity) {
            return $this->convert($userEntity);
        }, $usersEntity);
    }

    /**
     * Delete roles from a user
     *
     * @param \Api\SdkBundle\Entity\User $user
     * @param                             array Api\Sdk\Model\Role[]
     *
     * @return type
     */
    private function removeRoles(UserEntity $user, array $scopes)
    {
        foreach ($scopes as $scope) {
            $roleQuery  = new RoleQuery(['name' => $scope]);
            $scopeRoles = $this->getMediator()->getColleague('roleDoctrine')->getResult($roleQuery);

            foreach ($scopeRoles as $scopeRole) {
                $user->removeRole($scopeRole);
            }
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Add a role to a user
     *
     * @param \Api\SdkBundle\Entity\User $user
     * @param                             array Api\Sdk\Model\Role[]
     *
     * @return type
     */
    private function addRoles(UserEntity $user, array $roles)
    {
        if (count($roles) <= 0) {
            return;
        }

        $roleQuery = new RoleQuery(['ids' => array_map(function ($role) {
            return $role->getId();
        }, $roles)]);

        $roles = $this->getMediator()->getColleague('roleDoctrine')->getResult($roleQuery);

        foreach ($roles as $role) {
            $user->addRole($role);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Set roles to a user in specific scopes
     * Delete scopes linked with the user and add the roles provided
     *
     * @param Api\Sdk\Model\User   $user
     * @param Api\Sdk\Model\Role[] $roles
     * @param array                $scopes example : espri, espri_back, backoffice, lta_user
     *
     * @return void
     */
    public function setRoles(User $user, array $roles, array $scopes)
    {
        $user = $this->repository->find($user->getId());

        $this->removeRoles($user, $scopes);
        if (count($roles) > 0) {
            $this->addRoles($user, $roles);
        }

    }
    
    public function getAllUsers()
    {
        $query = new UserQuery();
        return $this->getCollection($query);
        ////return $this->get
    }

    /**
     * @param string $username
     *
     * @return array|null
     */
    public function getByUsername($username)
    {
        $query = new UserQuery(['username' => $username]);
        $user = $this->getOne($query);
        
        return (null === $user) ? null : $this->convert($user);
    }
}
