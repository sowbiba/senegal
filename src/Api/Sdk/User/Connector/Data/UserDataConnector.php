<?php
/**
 * Author: Florent Coquel
 * Date: 31/10/13
 */

namespace Api\Sdk\User\Connector\Data;

use Api\Sdk\Connector\AbstractDataConnector;
use Api\Sdk\Query\QueryInterface;

class UserDataConnector extends AbstractDataConnector
{
    /**
     * @inheritdoc
     *
     * @param int $id
     *
     * @return array|bool|float|int|mixed|null|number|string
     */
    public function getById($id)
    {
        return $this->getData('user', $id);
    }

    /**
     * @inheritdoc
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function getCollection(QueryInterface $query)
    {
        return $this->getDatas("user", $query);
    }

    /**
     * @inheritdoc
     *
     * @param QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query)
    {
        return count($this->getDatas("user", $query));
    }
}
