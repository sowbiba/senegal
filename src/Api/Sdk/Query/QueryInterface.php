<?php

namespace Api\Sdk\Query;

use Doctrine\ORM\QueryBuilder;

interface QueryInterface
{
    /**
     * Convert this query to a doctrine query
     * Need a doctrine entity manager to be executed
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function matchDoctrine(QueryBuilder $qb);

    /**
     * Convert this query to a sf1 propel criterion
     * Need a LegacyBridge::permissivetransaction to be executed
     *
     * @param \Criteria $criteria
     *
     * @return \Criterion
     */
    public function matchPropel(\Criteria &$criteria);

    /**
     * @return array
     */
    public function toArray();
}
