<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\TypePage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @Rest\NamePrefix("typepage_")
 */
class TypePageController extends ApiController
{
    /**
     * @ApiDoc(
     *     section="TypePage",
     *     description="Returns a collection of typePages",
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when no typePage are found"
     *     }
     * )
     *
     * @Rest\Get("/type-pages")
     *
     * @Rest\QueryParam(name="name", default=null, nullable=true, strict=false, description="Filter by name")
     * @Rest\QueryParam(key="serializerGroups", name="serializerGroups[]", default="type_page_list", array=true, requirements="(type_page_list, forfait_list, forfait_create, forfait_update, forfait_read)+", strict=false, description="The serializer groups")
     * @Rest\QueryParam(name="sortField", default="name", requirements="(name)+", strict=false, description="The sort field")
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
        $typePageList = $this->get('senegal_type_page_manager')->findByFilters(
            $this->cleanFilters($paramFetcher->all()),
            $paramFetcher->get('sortField'),
            $paramFetcher->get('sortOrder'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $statusCode = Response::HTTP_OK;
        if (!count($typePageList['typePages'])) {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        return FOSView::create()
            ->setStatusCode($statusCode)
            ->setData($typePageList)
            ->setSerializationContext(SerializationContext::create()->setGroups($paramFetcher->get('serializerGroups[]')))
        ;
    }

    /**
     * @ApiDoc(
     *     section="TypePage",
     *     description="Creates a typePage",
     *     statusCodes={
     *         201="Returned when successful",
     *         422="Returned when typePage is not valid"
     *     }
     * )
     *
     * @Rest\Post("/type-page")
     *
     * @Rest\RequestParam(name="name", strict=false, description="TypePage name")
     *
     * @Rest\View(statusCode="201", serializerGroups={"type_page_create"})
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return TypePage|Response
     */
    public function createAction(ParamFetcher $paramFetcher)
    {
        try {
            return $this->get('senegal_type_page_manager')->insert($paramFetcher->all());
        } catch (UnprocessableEntityHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @ApiDoc(
     *     section="TypePage",
     *     description="Returns a typePage",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when typePage not found"
     *     },
     *     requirements={
     *         {
     *             "name"="typePageId",
     *             "dataType"="integer",
     *             "required"=true,
     *             "requirement"="\d+",
     *             "description"="the typePage id"
     *         }
     *     }
     * )
     *
     * @Rest\Get("/type-page/{typePageId}", requirements={"typePageId"="\d+"})
     * @ParamConverter("typePage", class="SenegalApiBundle:TypePage", options={"id"="typePageId"})
     *
     * @Rest\View(serializerGroups={"type_page_read"})
     *
     * @param TypePage $typePage
     *
     * @return TypePage
     */
    public function readAction(TypePage $typePage)
    {
        return $typePage;
    }

    /**
     * @ApiDoc(
     *     section="TypePage",
     *     description="Edits a typePage",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when typePage not found",
     *         422="Returned when typePage is not valid"
     *     },
     *     requirements={
     *         {
     *             "name"="typePageId",
     *             "dataType"="integer",
     *             "required"=true,
     *             "requirement"="\d+",
     *             "description"="the typePage id"
     *         }
     *     }
     * )
     *
     * @Rest\Put("/type-page/{typePageId}", requirements={"typePageId"="\d+"})
     * @ParamConverter("typePage", class="SenegalApiBundle:TypePage", options={"id"="typePageId"})
     *
     * @Rest\RequestParam(name="name", strict=false, description="TypePage name")
     *
     * @Rest\View(serializerGroups={"type_page_update"})
     *
     * @param ParamFetcher $paramFetcher
     * @param TypePage         $typePage
     *
     * @return TypePage|Response
     */
    public function updateAction(ParamFetcher $paramFetcher, TypePage $typePage)
    {
        try {
            return $this->get('senegal_type_page_manager')->update($typePage, $paramFetcher->all());
        } catch (UnprocessableEntityHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @ApiDoc(
     *     section="TypePage",
     *     description="Deletes a type page",
     *     statusCodes={
     *         204="Returned when successful",
     *         404="Returned when type page not found"
     *     },
     *     requirements={
     *         {
     *             "name"="typePageId",
     *             "dataType"="integer",
     *             "required"=true,
     *             "requirement"="\d+",
     *             "description"="the type page id"
     *         }
     *     }
     * )
     *
     * @Rest\Delete("/type-page/{typePageId}", requirements={"typePageId"="\d+"})
     * @ParamConverter("typePage", class="SenegalApiBundle:TypePage", options={"id"="typePageId"})
     *
     * @Rest\View()
     *
     * @param TypePage $typePage
     *
     * @return Response
     */
    public function deleteAction(TypePage $typePage)
    {
        $this->get('senegal_type_page_manager')->delete($typePage);
    }
}
