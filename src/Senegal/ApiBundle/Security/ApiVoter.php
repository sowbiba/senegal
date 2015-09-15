<?php

namespace Senegal\ApiBundle\Security;

use Senegal\ApiBundle\Entity\Role as ApiRole;
use Senegal\SecurityBundle\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiVoter implements VoterInterface
{
    const SUPER_ADMIN = 'SUPER_ADMIN';
    const ACCOUNT_ADMIN = 'ACCOUNT_ADMIN';
    const USER = 'USER';

    private $roleHierarchy;

    public function __construct(RoleHierarchy $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [self::SUPER_ADMIN, self::ACCOUNT_ADMIN, self::USER]);
    }

    public function supportsClass($class)
    {
        return true;
    }

    public function vote(TokenInterface $token, $entity, array $attributes)
    {
        // todo: remove this when the security stories are done + add tests (see https://github.com/Behatch/contexts/issues/119)
        //return VoterInterface::ACCESS_GRANTED;

        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($entity))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow two attributes
        if (empty($attributes) || count($attributes) > 2) {
            throw new \InvalidArgumentException(
                'First attribute (action) needs to be SUPER_ADMIN, ACCOUNT_ADMIN or USER. The second optional attribute is the minimum role slug; If not given, if action = user.'
            );
        }

        $i=0;
        foreach ($attributes as $attribute) {
            if (
                0 <= strpos($attribute, 'ROLE_')
            ) {
                $attributes[$i] = str_replace('ROLE_', '', $attribute);
            }

            $i++;
        }

        // get the action
        $roleRequired = strtoupper($attributes[0]);

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($roleRequired)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        /**
         * @var User $user
         */
        $user = $token->getUser();

        //make an array with all user's roles
        $userRoles = [];
        if ($user instanceof UserInterface) {
            foreach ($user->getRolesNames() as $roleName) {
                $userRoles[] = new Role($roleName);
            }
        }

        /**
         * @var Role $role
         */
        foreach ($this->roleHierarchy->getReachableRoles($userRoles) as $role) {
            if ($role->getRole() === $roleRequired) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
