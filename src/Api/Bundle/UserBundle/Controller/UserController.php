<?php

namespace Api\Bundle\UserBundle\Controller;

use Api\SdkBundle\Controller\Controller;
use Api\Sdk\Model\User;
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
     *  description="user login",
     *  input={
     *      "class"="Api\Sdk\Model\User",
     *      "groups"={"update", "public"}
     *   }
     * )
     *
     * @param  $id
     * @return JsonResponse
     */
    public function saveUserAction(User $user)
    {
        error_log($user);
        error_log("ON EST LA");
        return json_encode("AHHH");
        return json_encode($request);
    }
}
