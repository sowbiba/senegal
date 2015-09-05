<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @Rest\NamePrefix("user_")
 */
class UserController extends ApiController
{
    /**
     * @ApiDoc(
     *     section="User",
     *     description="Returns a collection of users",
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when no user are found"
     *     }
     * )
     *
     * @Rest\Get("/users")
     *
     * @Rest\QueryParam(name="username", default=null, nullable=true, strict=false, description="Filter by username")
     * @Rest\QueryParam(name="lastname", default=null, nullable=true, strict=false, description="Filter by name")
     * @Rest\QueryParam(name="firstName", default=null, nullable=true, strict=false, description="Filter by first name")
     * @Rest\QueryParam(name="email", default=null, nullable=true, strict=false, description="Filter by email")
     * @Rest\QueryParam(name="active", default=null, nullable=true, requirements="(0|1)+", strict=false, description="Filter by activation status")
     * @Rest\QueryParam(name="roleId", nullable=true, strict=false, description="Filter by role id")
     * @Rest\QueryParam(key="serializerGroups", name="serializerGroups[]", default="user_list", array=true, requirements="(user_list)+", strict=false, description="The serializer groups")
     * @Rest\QueryParam(name="sortField", default="username", requirements="(active|email|firstName|group|name|receiveMail|role|testEndDate|updatedAt|username)+", strict=false, description="The sort field")
     * @Rest\QueryParam(name="sortOrder", default="asc", requirements="(asc|desc)+", strict=false, description="The sort order")
     * @Rest\QueryParam(name="limit", default=null, requirements="\d+", strict=false, description="The sort limit")
     * @Rest\QueryParam(name="offset", default=null, requirements="\d+", strict=false, description="The sort offset limit")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function listAction(ParamFetcher $paramFetcher)
    {
        $userList = $this->get('senegal_user_manager')->findByFilters(
            $this->cleanFilters($paramFetcher->all()),
            $paramFetcher->get('sortField'),
            $paramFetcher->get('sortOrder'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $statusCode = Response::HTTP_OK;
        if (!count($userList['users'])) {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        return FOSView::create()
            ->setStatusCode($statusCode)
            ->setData($userList)
            ->setSerializationContext(SerializationContext::create()->setGroups($paramFetcher->get('serializerGroups[]')))
        ;
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     description="Creates a user",
     *     statusCodes={
     *         201="Returned when successful",
     *         422="Returned when user is not valid"
     *     }
     * )
     *
     * @Rest\Post("/user")
     *
     * @Rest\RequestParam(name="role", strict=false, description="User role id")
     * @Rest\RequestParam(name="username", strict=false, description="User username")
     * @Rest\RequestParam(name="password", strict=false, description="User password")
     * @Rest\RequestParam(name="email", strict=false, description="User email")
     * @Rest\RequestParam(name="firstname", strict=false, description="User firstname")
     * @Rest\RequestParam(name="lastname", strict=false, description="User name")
     * @Rest\RequestParam(name="active", default=0, strict=false, description="Is the user active ?")
     * @Rest\RequestParam(name="address", strict=false, description="User address")
     * @Rest\RequestParam(name="phone", strict=false, description="User phone number")
     *
     * @Rest\View(statusCode="201", serializerGroups={"user_create"})
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return User|Response
     */
    public function createAction(ParamFetcher $paramFetcher)
    {
        try {
            return $this->get('senegal_user_manager')->insert($paramFetcher->all());
        } catch (UnprocessableEntityHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     description="Returns a user",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when user not found"
     *     },
     *     requirements={
     *         {
     *             "name"="userId",
     *             "dataType"="integer",
     *             "required"=true,
     *             "requirement"="\d+",
     *             "description"="the user id"
     *         }
     *     }
     * )
     *
     * @Rest\Get("/user/{userId}", requirements={"userId"="\d+"})
     * @ParamConverter("user", class="SenegalApiBundle:User", options={"id"="userId"})
     *
     * @Rest\View(serializerGroups={"user_read"})
     *
     * @param User $user
     *
     * @return User
     */
    public function readAction(User $user)
    {
        return $user;
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     description="Edits a user",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when user not found",
     *         422="Returned when user is not valid"
     *     },
     *     requirements={
     *         {
     *             "name"="userId",
     *             "dataType"="integer",
     *             "required"=true,
     *             "requirement"="\d+",
     *             "description"="the user id"
     *         }
     *     }
     * )
     *
     * @Rest\Put("/user/{userId}", requirements={"userId"="\d+"})
     * @ParamConverter("user", class="SenegalApiBundle:User", options={"id"="userId"})
     *
     * @Rest\RequestParam(name="role", strict=false, description="User role id")
     * @Rest\RequestParam(name="username", strict=false, description="User username")
     * @Rest\RequestParam(name="password", strict=false, description="User password")
     * @Rest\RequestParam(name="email", strict=false, description="User email")
     * @Rest\RequestParam(name="firstName", strict=false, description="User firstname")
     * @Rest\RequestParam(name="lastname", strict=false, description="User name")
     * @Rest\RequestParam(name="active", default=0, strict=false, description="Is the user active ?")
     * @Rest\RequestParam(name="address", strict=false, description="User address")
     * @Rest\RequestParam(name="phone", strict=false, description="User phone number")
     *
     * @Rest\View(serializerGroups={"user_update"})
     *
     * @param ParamFetcher $paramFetcher
     * @param User         $user
     *
     * @return User|Response
     */
    public function updateAction(ParamFetcher $paramFetcher, User $user)
    {
        try {
            return $this->get('senegal_user_manager')->update($user, $paramFetcher->all());
        } catch (UnprocessableEntityHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }
}
