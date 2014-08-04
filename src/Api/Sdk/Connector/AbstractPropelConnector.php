<?php
/**
 * Author: Florent Coquel
 * Date: 31/10/13
 */

namespace Api\Sdk\Connector;

use Api\Sdk\Bridge\LegacyBridge;

class AbstractPropelConnector extends AbstractConnector
{
    private $bridge;

    public function __construct(LegacyBridge $bridge)
    {
        $this->bridge = $bridge;
    }

    public function getBridge()
    {
        return $this->bridge;
    }

    public function shutdown()
    {
        $this->bridge->shutdown();
    }

    public function boot()
    {
        $this->bridge->boot();
    }
}
