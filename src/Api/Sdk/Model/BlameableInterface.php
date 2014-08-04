<?php

namespace Api\Sdk\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BlameableInterface
 */
interface BlameableInterface
{
    public function setCreatedBy(UserInterface $createdBy);

    public function setUpdatedBy(UserInterface $updatedBy);
}
