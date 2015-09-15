<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\Role;
use Senegal\ApiBundle\Serializer\Exclusion\FieldsListExclusionStrategy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Senegal\ApiBundle\Model\Collection;

/**
 * @Rest\NamePrefix("role_")
 */
class RoleController extends ApiController
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
     *     section="Role",
     *     description="List roles",
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
     * @Rest\Get("/roles")
     * @ParamConverter("roles", class="SenegalApiBundle:Role", converter="collection_param_converter", options={"name"="roles"})
     *
     * @Security("is_granted('SUPER_ADMIN')")
     *
     * @param Request    $request
     * @param Collection $roles
     *
     * @return FOSView
     */
    public function listAction(Request $request, Collection $roles)
    {
        if ('' !== $fields = $request->query->get('fields', '')) {
            $fields = array_merge(explode(',', $fields), ['roles']);
        }

        return $this->createView($request, $roles)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'role_list'])
                    ->addExclusionStrategy(
                    // todo: Use Role::class when the PHP version is >= 5.5
                        new FieldsListExclusionStrategy('Senegal\ApiBundle\Entity\Role', $fields)
                    )
            );
    }
}
