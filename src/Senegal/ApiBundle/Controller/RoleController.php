<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\NamePrefix("role_")
 */
class RoleController extends ApiController
{
    /**
     * @ApiDoc(
     *     section="Role",
     *     description="Returns a collection of roles",
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when no role are found"
     *     }
     * )
     *
     * @Rest\Get("/roles")
     *
     * @Rest\QueryParam(key="serializerGroups", name="serializerGroups[]", default="role_list", array=true, requirements="(role_list|user_create|user_update)+", strict=false, description="The serializer groups")
     * @Rest\QueryParam(name="sortField", default="description", requirements="(description|name)+", strict=false, description="The sort field")
     * @Rest\QueryParam(name="sortOrder", default="asc", requirements="(asc|desc)+", strict=false, description="The sort order")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function listAction(ParamFetcher $paramFetcher)
    {
        $roleList = $this->get('senegal_role_manager')->findBy(
            [],
            [$paramFetcher->get('sortField') => $paramFetcher->get('sortOrder')]
        );

        $statusCode = Response::HTTP_OK;
        if (!count($roleList)) {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        return FOSView::create()
            ->setStatusCode($statusCode)
            ->setData($roleList)
            ->setSerializationContext(SerializationContext::create()->setGroups($paramFetcher->get('serializerGroups[]')))
        ;
    }
}
