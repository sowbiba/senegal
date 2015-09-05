<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\Forfait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @Rest\NamePrefix("forfait_")
 */
class ForfaitController extends ApiController
{
    /**
     * @ApiDoc(
     *     section="Forfait",
     *     description="Returns a collection of forfaits",
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when no forfait are found"
     *     }
     * )
     *
     * @Rest\Get("/forfaits")
     *
     * @Rest\QueryParam(name="environment", default="api", requirements="(back|front)+", description="In which environment ?")
     * @Rest\QueryParam(key="serializerGroup", name="serializerGroup", default="forfait_list", requirements="(forfait_list|user_create)+", strict=false, description="The serializer group")
     *
     * @Rest\QueryParam(name="name", default=null, nullable=true, strict=false, description="Filter by name")
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
        $forfaitList = $this->get('senegal_forfait_manager')->findByFilters(
            $this->cleanFilters($paramFetcher->all()),
            $paramFetcher->get('sortField'),
            $paramFetcher->get('sortOrder'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $statusCode = Response::HTTP_OK;
        if (!count($forfaitList['forfaits'])) {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        return FOSView::create()
            ->setStatusCode($statusCode)
            ->setData($forfaitList)
            ->setSerializationContext(SerializationContext::create()->setGroups([$this->getBaseSerializerGroup($paramFetcher->all())]))
        ;
    }

    /**
     * @ApiDoc(
     *     section="Forfait",
     *     description="Creates a forfait",
     *     statusCodes={
     *         201="Returned when successful",
     *         422="Returned when forfait is not valid"
     *     }
     * )
     *
     * @Rest\Post("/forfait")
     *
     * @Rest\RequestParam(name="name", strict=false, description="Forfait name")
     *
     * @Rest\View(statusCode="201", serializerGroups={"forfait_create"})
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Forfait|Response
     */
    public function createAction(ParamFetcher $paramFetcher)
    {
        try {
            return $this->get('senegal_forfait_manager')->insert($paramFetcher->all());
        } catch (UnprocessableEntityHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @ApiDoc(
     *     section="Forfait",
     *     description="Returns a forfait",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when forfait not found"
     *     },
     *     requirements={
     *         {
     *             "name"="forfaitId",
     *             "dataType"="integer",
     *             "required"=true,
     *             "requirement"="\d+",
     *             "description"="the forfait id"
     *         }
     *     }
     * )
     *
     * @Rest\Get("/forfait/{forfaitId}", requirements={"forfaitId"="\d+"})
     * @ParamConverter("forfait", class="SenegalApiBundle:Forfait", options={"id"="forfaitId"})
     *
     * @Rest\View(serializerGroups={"forfait_read"})
     *
     * @param Forfait $forfait
     *
     * @return Forfait
     */
    public function readAction(Forfait $forfait)
    {
        return $forfait;
    }

    /**
     * @ApiDoc(
     *     section="Forfait",
     *     description="Edits a forfait",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when forfait not found",
     *         422="Returned when forfait is not valid"
     *     },
     *     requirements={
     *         {
     *             "name"="forfaitId",
     *             "dataType"="integer",
     *             "required"=true,
     *             "requirement"="\d+",
     *             "description"="the forfait id"
     *         }
     *     }
     * )
     *
     * @Rest\Put("/forfait/{forfaitId}", requirements={"forfaitId"="\d+"})
     * @ParamConverter("forfait", class="SenegalApiBundle:Forfait", options={"id"="forfaitId"})
     *
     * @Rest\RequestParam(name="name", strict=false, description="Forfait name")
     * @Rest\RequestParam(name="forfaitTypePages", strict=false, description="Forfait name")
     *
     * @Rest\View(serializerGroups={"forfait_update"})
     *
     * @param ParamFetcher $paramFetcher
     * @param Forfait         $forfait
     *
     * @return Forfait|Response
     */
    public function updateAction(ParamFetcher $paramFetcher, Forfait $forfait)
    {
        try {
            return $this->get('senegal_forfait_manager')->update($forfait, $paramFetcher->all());
        } catch (UnprocessableEntityHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }
}
