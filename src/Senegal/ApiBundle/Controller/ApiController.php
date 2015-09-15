<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\View\View;
use Senegal\ApiBundle\Representation\NotPaginatedRepresentation;
use Symfony\Component\HttpFoundation\Request;
use Senegal\ApiBundle\Model\Collection;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\LessThan1MaxPerPageException;
use Pagerfanta\Pagerfanta;

/**
 * @codeCoverageIgnore
 */
abstract class ApiController extends FOSRestController
{
    const DEFAULT_NUM_ITEMS = 20;

    /**
     * Creates a view.
     *
     * Convenience method to allow for a fluent interface.
     *
     * @param Request $request
     * @param mixed $data
     * @param int   $statusCode
     * @param array $headers
     *
     * @return View
     */
    protected function createView(Request $request, $data = null, $statusCode = null, array $headers = array())
    {
        return $this->view($this->representData($request, $data), $statusCode, $headers);
    }
    /**
     * This method removes serializerGroups, serializerGroups[], sortField, sortOrder and
     * all null or empty filters from the list.
     *
     * @param array $filters
     * @param array $specificClean
     *
     * @return array
     */
    protected function cleanFilters(array $filters = [], array $specificClean = [])
    {
        return array_filter(
            array_diff_key(
                $filters,
                array_merge(['environment' => '', 'serializerGroups' => '', 'serializerGroups[]' => '', 'serializerGroup' => '', 'sortField' => '', 'sortOrder' => ''], $specificClean)
            ), function ($val) {
                return !is_null($val) && '' !== $val;
            }
        );
    }

    protected function getBaseSerializerGroup($params)
    {
        return "{$params['environment']}_{$params['serializerGroup']}";
    }

    /**
     * Serializes the given data to the specified output format, using JMS Serializer.
     *
     * @param object|array         $data
     * @param string               $format
     * @param SerializationContext $context
     *
     * @return string
     */
    protected function serialize($data, $format = 'json', SerializationContext $context = null)
    {
        return $this->get('jms_serializer')->serialize($data, $format, $context);
    }

    /**
     * @param Request $request
     * @param $data
     * @return array|\Hateoas\Representation\PaginatedRepresentation|NotPaginatedRepresentation
     */
    protected function representData(Request $request, $data)
    {
        // We add a HATEOAS representation to the data result, if the data is a collection.
        if ($data instanceof Collection) {
            $rel = strval($data);
            $xmlElementName = strval($data);

            $data = $data->getArrayCopy();

            $queries = $request->query->all();
            unset($queries['page']); // The page query will be added by the pager later.

            $routeName = $request->get('_route');
            $routeParameters = array_merge($request->get('_route_params'), $queries);

            if (!empty($data) && !$request->query->has('limit')) {
                if (method_exists(reset($data), "getDefaultPaginationNumItems")) {
                    // Call the getDefaultPaginationNumItems() method of the first element of the array.
                    $limit = (int) reset($data)->getDefaultPaginationNumItems();
                } else {
                    $limit = self::DEFAULT_NUM_ITEMS;
                }
            } else {
                $limit = (int) $request->query->get('limit', self::DEFAULT_NUM_ITEMS);
            }

            // Create a PaginatedRepresentation or a NotPaginatedRepresentation depending of the request
            // parameters and the traits used by the entity.
            if (0 > $limit) {
                if (0 === $limit) {
                    throw new LessThan1MaxPerPageException();
                } elseif (0 < $limit) {
                    $data = array_slice($data, 0, $limit);
                }

                $representation = new NotPaginatedRepresentation(
                    new CollectionRepresentation($data, $rel, $xmlElementName),
                    $routeName,
                    $routeParameters,
                    false,
                    count($data)
                );
            } else {
                $page = $request->query->get('page', 1);

                $pager = new Pagerfanta(new ArrayAdapter($data));
                $pager->setMaxPerPage($limit);
                $pager->setCurrentPage($page);

                $pagerFactory = new PagerfantaFactory();
                $representation = $pagerFactory->createRepresentation(
                    $pager,
                    new Route($routeName, $routeParameters),
                    new CollectionRepresentation($pager->getCurrentPageResults(), $rel, $xmlElementName)
                );
            }

            return $representation;
        }

        return $data;
    }
}
