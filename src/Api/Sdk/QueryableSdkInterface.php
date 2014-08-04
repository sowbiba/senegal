<?php

namespace Api\Sdk;

use Api\Sdk\Query\QueryInterface;

interface QueryableSdkInterface extends SdkInterface
{
    /**
     * @param array $filters
     *
     * @return \Api\Sdk\Query\QueryInterface
     */
    public function getQuery(array $filters = array());

    /**
     * @param QueryInterface $query
     * @param array          $sorts
     *
     * @return \Api\Sdk\Query\QueryInterface
     */
    public function getSortQuery(QueryInterface $query, array $sorts = array());
}
