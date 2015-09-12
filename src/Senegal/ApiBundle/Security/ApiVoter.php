<?php

namespace Senegal\ApiBundle\Security;

use Senegal\ApiBundle\Entity\Role as ApiRole;
use Senegal\ApiBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiVoter implements VoterInterface
{
    const SUPER_ADMIN = 'super_admin';
    const ACCOUNT_ADMIN = 'account_admin';
    const USER = 'user';

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

        // get the action
        $action = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($action)) {
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

        switch ($action) {
            case self::ACCOUNT_ADMIN:
                $expectedRole = ApiRole::ACCOUNT_ADMIN_ROLE;
                break;
            case self::SUPER_ADMIN: //view is allowed for all users
                $expectedRole = ApiRole::SUPER_ADMIN_ROLE;
                break;
            default: //view is allowed for all users
                $expectedRole = ApiRole::USER_ROLE;
                break;
        }

        /**
         * @var Role $role
         */
        foreach ($this->roleHierarchy->getReachableRoles($userRoles) as $role) {
            if ($role->getRole() === $expectedRole) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
