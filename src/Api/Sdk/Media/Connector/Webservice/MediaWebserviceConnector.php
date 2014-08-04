<?php

namespace Api\Sdk\Media\Connector\Webservice;

use Api\Sdk\Connector\AbstractWebserviceConnector;

class MediaWebserviceConnector extends AbstractWebserviceConnector
{
    public function getAll()
    {
        $result   = array();
        $response = $this->getClient()->get("medias")->send();
        if ($response->isSuccessful()) {
            foreach ($response->json() as $id => $label) {
                $result[] = array("id" => $id, "label" => $label);
            }
        }

        return $result;
    }
}
