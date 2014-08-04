<?php

namespace Api\Sdk\Connector;

use Guzzle\Http\ClientInterface;

class AbstractWebserviceConnector extends AbstractConnector
{

    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return \Guzzle\Http\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
}
