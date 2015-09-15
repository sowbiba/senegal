<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\User;
use Senegal\ApiBundle\Serializer\Exclusion\FieldsListExclusionStrategy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Senegal\ApiBundle\Model\Collection;

/**
 * @Rest\NamePrefix("user_")
 */
class UserController extends ApiController
{
    /**
     * Gets the list of users.
     *
     * <hr>
     *
     * After getting the initial list, use the <strong>first, last, next, prev</strong> link relations in the
     * <strong>_links</strong> property to get more users in the list. Note that <strong>next</strong> will not be
     * available at the end of the list and <strong>prev</strong> will not be available at the start of the list. If
     * the results are exactly one page neither <strong>prev</strong> nor <strong>next</strong> will be available.
     *
     * The <strong>_embedded</strong> embedded user resources key'ed by relation name.
     *
     * <hr>
     *
     * The filters allows you to use the percent sign and underscore wildcards (e.g. name, %name, name%, %name%,
     * na_e, n%e).
     *
     * @ApiDoc(
     *     section="User",
     *     description="List users",
     *     statusCodes={
     *         200="OK",
     *         400="Bad request",
     *         403="Forbidden",
     *     },
     *     parameters={
     *         {
     *             "name"="fields",
     *             "dataType"="string",
     *             "description"="Specify the fields that will be returned using the format FIELD_NAME[, FIELD_NAME ...]. Valid fields are id and name. e.g. If you want the result with the name field only, the fields string would be name. Default is: all the fields.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="orderBy",
     *             "dataType"="string",
     *             "description"="Specify the order criteria of the result using the format COLUMN_NAME ORDER[, COLUMN_NAME ORDER ...]. Valid column names are id and name. Valid orders are asc and desc. e.g. If you want the user ordered by name in descending order and then order by id in ascending order, the order string would be name=desc, id=asc. Default is: id asc.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="page",
     *             "dataType"="integer",
     *             "description"="Current page to returned. Default is: 1.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="limit",
     *             "dataType"="integer",
     *             "description"="Maximum number of items requested (-1 for no limit). Default is: 10.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Get("/users")
     * @ParamConverter("users", class="SenegalApiBundle:User", converter="collection_param_converter", options={"name"="users"})
     *
     * @Security("is_granted('SUPER_ADMIN')")
     *
     * @param Request    $request
     * @param Collection $users
     *
     * @return FOSView
     */
    public function listAction(Request $request, Collection $users)
    {
        if ('' !== $fields = $request->query->get('fields', '')) {
            $fields = array_merge(explode(',', $fields), ['users']);
        }

        return $this->createView($request, $users)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'user_list'])
                    ->addExclusionStrategy(
                    // todo: Use User::class when the PHP version is >= 5.5
                        new FieldsListExclusionStrategy('Senegal\ApiBundle\Entity\User', $fields)
                    )
            );
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
