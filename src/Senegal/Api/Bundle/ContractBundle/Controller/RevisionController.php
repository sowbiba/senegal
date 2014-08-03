<?php

namespace Senegal\Api\Bundle\ContractBundle\Controller;

use Senegal\Api\Bundle\ApiController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class RevisionController
 * @package Senegal\Api\Bundle\ContractBundle\Controller
 */
class RevisionController extends ApiController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns revision",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found, or incorrect status given, or revision not found"
     *  }
     * )
     *
     * @param $id
     * @param $status
     * @return Revision
     */
    public function getAction($id, $status)
    {
        return $this->retrieveRevision($id, $status);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns revision documents",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found, or incorrect status given, or revision not found"
     *  }
     * )
     *
     * @param $id
     * @param $status
     * @return \Pfd\Sdk\Model\Document[]
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getDocumentsAction($id, $status)
    {
        $revision = $this->retrieveRevision($id, $status);
        $query = new DocumentQuery(array('excludedTypes' => [17]));

        return $revision->getDocuments($query);
    }

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns revision sources",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found, or incorrect status given, or revision not found"
     *  }
     * )
     *
     * @param $id
     * @param $status
     * @return array
     */
    public function getSourcesAction($id, $status){
        $revision = $this->retrieveRevision($id, $status);

        return $revision->getFieldSources();
    }

}
