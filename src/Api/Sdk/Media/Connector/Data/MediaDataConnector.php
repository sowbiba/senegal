<?php
/**
 * Author: Florent Coquel
 * Date: 31/10/13
 */

namespace Api\Sdk\Media\Connector\Data;

use Api\Sdk\Connector\AbstractDataConnector;
use Api\Sdk\Query\QueryInterface;

class MediaDataConnector extends AbstractDataConnector
{

     /**
     * @inheritdoc
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function getCollection(QueryInterface $query)
    {
        return $this->getDatas("media", $query);
    }

    public function getAll()
    {
        return $this->getDatas('media');
    }
}
