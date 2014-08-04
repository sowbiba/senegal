<?php
/**
 * Author: Florent Coquel
 * Date: 24/09/13
 */
namespace Api\Sdk\Mediator;

class ConnectorMediator extends AbstractMediator
{
    /**
     * Returns the name of the given connector
     *
     * @param ColleagueInterface $connector
     *
     * @return string
     */
    protected function getName(ColleagueInterface $connector)
    {
        $explodeClassName = explode("\\", get_class($connector));
        $connectorName    = str_replace("Connector", "", lcfirst(array_pop($explodeClassName)));

        return $connectorName;
    }
}
