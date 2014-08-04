<?php
namespace Api\Sdk\Connector;

use Api\Sdk\Bridge\LegacyBridge;

/**
 * Implements a connection towards Propel (i.e. legacy system)
 *
 * Globally, this class implements methods defined in sdk.
 *
 * Please note that we have created a generic filters mechanism to retrieve objects (see FiltersToPropelConverter
 * for more information), that we use for example in getAllDistributrs() and getDistributorsByContractId($contractId) methods.
 *
 * We may also have used it in getContract(), getProductLine() and so on, but it was simpler and faster that way...
 *
 * Class PropelConnector
 * @package Api\Sdk\Connector
 * @author Florent Coquel
 * @since 20/05/13
 *
 * Can't test it without a context (database)
 * Normally methods are tested in the backoffice
 * @codeCoverageIgnore
 */
class PropelConnector extends AbstractConnector
{

    /**
     * @var \Api\Sdk\Bridge\LegacyBridge
     */
    private $bridge;

    /**
     * @param LegacyBridge $bridge
     */
    public function __construct(LegacyBridge $bridge)
    {
        $this->bridge = $bridge;
    }

    /**
     * Returns a collection of companies data
     *
     * @return array
     */
    public function getCompanies()
    {
        return $this->bridge->permissiveTransaction(function () {
            $companies = array();

            foreach (\SocietesPeer::getListOrderedByName() as $productLine) {
                $companies[] = $this->convertCompany($productLine);
            }

            return $companies;
        });
    }

    /**
     * @param $id
     *
     * @return \Api\Sdk\Bridge\type
     */
    public function getUser($id)
    {
        $user = $this->bridge->permissiveTransaction(function () use ($id) {
            return \sfGuardUserPeer::retrieveByPK($id);
        });

        return $this->convertUser($user);
    }
}
