<?php

namespace Senegal\Api\SdkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Senegal\Api\SdkBundle\Entity\CompanyGroup
 *
 * @codeCoverageIgnore
 */
class CompanyGroup extends BaseEntity
{
    protected $name;
    protected $companies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
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
    public function getCompanies()
    {
        return $this->companies;
    }

}
