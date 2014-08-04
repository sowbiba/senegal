<?php

namespace Api\Bundle\ContractBundle\Controller;

use Api\Bundle\ApiController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class ContractController
 * @package Api\Bundle\ContractBundle\Controller
 */
class ContractController extends ApiController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns contract data",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found"
     *  }
     * )
     *
     * @param $id
     * @return Contract
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAction($id)
    {
        /** @var Contract $contract */
        $contract = $this->getSdk('contract')->getById(intval($id));
        if (null === $contract) {
            throw $this->createNotFoundException();
        }

        return $contract;
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns all documents data",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found"
     *  }
     * )
     *
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAllDocumentsAction()
    {
        /** @var Contract $contract */
        return $this->getSdk('document')->getDocumentTypes();

        $query = new DocumentQuery(array('excludedTypes' => [17]));

        return $contract->getDocuments($query);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns contract documents data",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found"
     *  }
     * )
     *
     * @param $id
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getDocumentsAction($id)
    {
        /** @var Contract $contract */
        $contract = $this->getSdk('contract')->getById(intval($id));
        if (null === $contract) {
            throw $this->createNotFoundException();
        }

        $query = new DocumentQuery(array('excludedTypes' => [17]));

        return $contract->getDocuments($query);
    }


    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns contract sources",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found, or incorrect status given, or revision not found"
     *  }
     * )
     *
     * @param $id
     * @return array
     */
    public function getSourcesAction($id){
        $contract = $this->retrieveContract($id);

        return $contract->getFieldSources();
    }
}
