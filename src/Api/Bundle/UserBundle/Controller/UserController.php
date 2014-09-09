<?php

namespace Api\Bundle\UserBundle\Controller;

use Api\SdkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns all users",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found"
     *  }
     * )
     *
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAllUsersAction()
    {
        /** @var Contract $contract */
        return $this->getSdk('user')->getAllUsers();
    }
    
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns all users",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found"
     *  },
     * )
     *
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getUserAction($id)
    {
        /** @var User $user */
        return $this->getSdk('user')->getById((int)$id);
    }
    
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Post user form",
     *  statusCodes={
     *         200="Returned when successful",
     *         404="Returned when contract not found"
     *  },
     *  requirements={
     *      {
     *          "name"="login",
     *          "dataType"="string",
     *          "description"="user login"
     *      },
     *      {
     *          "name"="password",
     *          "dataType"="string",
     *          "description"="user password"
     *      }
     *  }
     * )
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function saveUserAction(Request $request)
    {
        return json_encode($request);
        /** @var Contract $contract */
        //return $this->getSdk('user')->getAllUsers();
    }
}
