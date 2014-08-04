<?php
namespace Api\Bundle\ContractBundle\Controller;


use Api\Bundle\ApiController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class DocumentController extends ApiController{

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns document",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when document not found"
     *  }
     * )
     *
     * @param $id
     * @return Document
     */
    public function getAction($id)
    {
        return $this->retrieveDocument($id);
    }
} 