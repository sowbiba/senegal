<?php
namespace Api\Sdk\Role\Query;


use Doctrine\ORM\QueryBuilder;

/**
 * This class allows you to retrieve only the roles for EsPri indexes
 *
 * Class RoleEspriQuery
 * @package Api\Sdk\Role\Query
 */
class RoleEspriQuery  extends RoleQuery {

    const ROLE_ESPRI_INDEX = "espri_front_indexes";

    private $espriIndexes = array("espri_front_market", "espri_front_media", "espri_front_companytype");

    /**
     * Build query with sets
     *
     * @param Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return Doctrine\ORM\QueryBuilder The one in parameter populated with filters
     */
    public function matchDoctrine(QueryBuilder $qb)
    {
        if (isset($this->filters['name']) && $this->filters['name'] == self::ROLE_ESPRI_INDEX) {
            $conditions = "";
                $i=0;
                foreach($this->espriIndexes as $name){
                    $qb->setParameter('name' . $i, $name . '%');
                    $conditions .= $i >= 1 ? " OR " . $qb->expr()->like('r.name', ":name$i") : $qb->expr()->like('r.name', ":name$i");
                    $i++;
                }
                $qb->andWhere($conditions);
        }

        return $qb;
    }

} 