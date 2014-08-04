<?php

namespace Api\Sdk\Contract\Connector\Data;

use Api\Sdk\Connector\AbstractDataConnector;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\Model\Contract;

/**
 * This is use in order to retrieve fake data
 *
 * Class DataConnector
 * @package Api\Sdk\Connector
 * @author  Florent Coquel
 * @since   14/06/13
 *
 * @SuppressWarnings(PHPMD)
 *
 */
class ContractDataConnector extends AbstractDataConnector
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
        return $this->getData('contract', $id);
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
        return $this->getDatas("contract", $query);
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
        return count($this->getDatas("contract", $query));
    }

    public function getRevisions(QueryInterface $query)
    {
        return count($this->getDatas("revision", $query));
    }

    /**
     * Return current contract of future contract
     */
    public function getCurrent(Contract $futureContract)
    {
        $futureContract = $this->getData('contract', $futureContract->getId());

        return $this->getData('contract', $futureContract['currentId']);
    }
}
