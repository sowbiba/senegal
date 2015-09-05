<?php

namespace Senegal\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserHasForfait
 *
 * @ORM\Table(name="user_has_forfait")
 * @ORM\Entity
 */
class UserHasForfait
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="forfait_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $forfaitId;



    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserHasForfait
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set forfaitId
     *
     * @param integer $forfaitId
     * @return UserHasForfait
     */
    public function setForfaitId($forfaitId)
    {
        $this->forfaitId = $forfaitId;

        return $this;
    }

    /**
     * Get forfaitId
     *
     * @return integer 
     */
    public function getForfaitId()
    {
        return $this->forfaitId;
    }
}
