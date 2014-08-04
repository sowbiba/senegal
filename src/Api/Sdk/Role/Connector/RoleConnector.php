<?php

namespace Api\Sdk\Role\Connector;

use Api\Sdk\Connector\AbstractConnector;
use Api\Sdk\Model\Role;
use Api\Sdk\Role\Query\RoleEspriQuery;

/**
 * Class RoleConnector
 */
class RoleConnector extends AbstractConnector implements RoleConnectorInterface
{
    public function preExecute()
    {
        // need to load EsPri roles in SGBD before any request
        $markets = $this->getMediator()->getColleague("market")->getAll();
        $medias = $this->getMediator()->getColleague("media")->getAll();
        $companyTypes = $this->getMediator()->getColleague("companyType")->getAll();
        $espriIndexes = $this->getConnectorToUse('getCollection')->getCollection(new RoleEspriQuery(['name' => RoleEspriQuery::ROLE_ESPRI_INDEX]));

        $espriRoles = array();

        foreach ($markets as $market) {
            $espriRoles["espri_front_market_" . $market["id"]] = array("description" => $market["label"], "name" => "espri_front_market_" . $market["id"]);
        }

        foreach ($medias as $media) {
            $espriRoles["espri_front_media_" . $media["id"]] = array("description" => $media["label"], "name" => "espri_front_media_" . $media["id"]);
        }

        foreach ($companyTypes as $companyType) {
            $espriRoles["espri_front_companytype_" . $companyType["id"]] = array("description" => $companyType["label"], "name" => "espri_front_companytype_" . $companyType["id"]);
        }

        foreach ($espriRoles as $espriRole) {
            if (false === $this->getByName($espriRole["name"])) {
                $this->getConnectorToUse("create")->create($espriRole);
            } else {
                $this->getConnectorToUse("updateByName")->updateByName($espriRole);
            }
        }
        /** @var Role $role */
        foreach($espriIndexes as $role){
            if(!array_key_exists($role['name'], $espriRoles)){
                $this->getConnectorToUse("delete")->delete($role);
            }
        }

        return true;
    }

    /**
     * Retrieve role by name (ex : backoffice_user)
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getByName($name)
    {
        return $this->getConnectorToUse("getByName")->getByName($name);
    }

}
