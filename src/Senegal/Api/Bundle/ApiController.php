<?php
/**
 * Created by PhpStorm.
 * User: fcoquel
 * Date: 11/06/14
 * Time: 15:40
 */

namespace Senegal\Api\Bundle;


use FOS\RestBundle\Routing\ClassResourceInterface;
use Senegal\Api\Model\User;
use Senegal\Api\SdkBundle\Controller\Controller;

class ApiController extends Controller  implements ClassResourceInterface {
    /**
     * @param $contractId
     * @param $status
     * @return Revision
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
//    protected function retrieveRevision($contractId, $status)
//    {
//        /** @var Contract $contract */
//        $contract = $this->getSdk('contract')->getById(intval($contractId));
//        if (null === $contract) {
//            throw $this->createNotFoundException();
//        }
//        $constantName = 'Pfd\Sdk\Model\Revision::STATUS_' . strtoupper($status);
//        if (!defined($constantName)) {
//            throw $this->createNotFoundException();
//        }
//
//        /** @var Revision $revision */
//        $revision = $contract->getRevisionWithStatus(constant($constantName));
//
//        if (null === $revision) {
//            throw $this->createNotFoundException();
//        }
//
//        return $revision;
//    }

    /**
     * @param $contractId
     * @return Contract
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
//    protected function retrieveContract($contractId)
//    {
//        /** @var Contract $contract */
//        $contract = $this->getSdk('contract')->getById(intval($contractId));
//        if (null === $contract) {
//            throw $this->createNotFoundException();
//        }
//
//
//        return $contract;
//    }


    /**
     * @param $documentId
     * @return Document
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
//    protected function retrieveDocument($documentId)
//    {
//        /** @var Document $document */
//        $document = $this->getSdk('document')->getById(intval($documentId));
//        if (null === $document) {
//            throw $this->createNotFoundException();
//        }
//
//
//        return $document;
//    }

} 