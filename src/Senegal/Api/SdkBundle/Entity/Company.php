<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Senegal\Api\SdkBundle\Entity\Company
 *
 * @codeCoverageIgnore
 */
class Company extends BaseEntity
{
    protected $name;
    protected $contracts;
    protected $groupesocietes;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * @return mixed
     */
    public function getCompanyGroup()
    {
        return $this->groupesocietes;
    }
}
