<?php

namespace Senegal\ApiBundle\Listener;

use Senegal\ApiBundle\Model\Collection;
use Senegal\ApiBundle\Representation\NotPaginatedRepresentation;
use FOS\RestBundle\EventListener\ViewResponseListener as FosViewResponseListener;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\LessThan1MaxPerPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ViewResponseListener extends FosViewResponseListener
{
    const DEFAULT_NUM_ITEMS = 20;

    /**
     * @param GetResponseForControllerResultEvent $event
     *
     * @return void
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $data = $event->getControllerResult()->getData();

        // We add a HATEOAS representation to the data result, if the data is a collection.
        if ($data instanceof Collection) {
            $request = $event->getRequest();

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

            $event->getControllerResult()->setData($representation);
        }

        parent::onKernelView($event);
    }
}
