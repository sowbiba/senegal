<?php

namespace Senegal\ApiBundle\ParamConverter;

use Senegal\ApiBundle\Model\Collection;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

/**
 * Converts request parameters to a collection of objects (ordered and filtered) and stores
 * them as request attributes, so they can be injected as controller method arguments.
 */
class CollectionParamConverter implements ParamConverterInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     *
     * @return bool
     *
     * @throws \BadMethodCallException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (!isset($options['name'])) {
            throw new \BadMethodCallException('The "name" option is required.');
        } elseif (!is_string($options['name']) || '' === $options['name']) {
            throw new \BadMethodCallException('The "name" option must be a not empty string.');
        }

        $repository = $this->entityManager->getRepository($configuration->getClass());
        $entityName = $repository->getClassName();

        $queries = $request->query->all();

        $orderBy = null;
        if (isset($queries['orderBy'])) {
            $orderBy = [];

            foreach (explode(',', $queries['orderBy']) as $order) {
                $order = explode(' ', trim($order));

                if (!isset($order[1])) {
                    throw new \BadMethodCallException('Wrong orderBy parameter format (COLUMN_NAME ORDER[, COLUMN_NAME ORDER ...])');
                }

                $orderBy[$order[0]] = $order[1];
            }
        }

        if (empty($orderBy)) {
            $entity = new $entityName();

            if (method_exists($entity, "getDefaultSortOrder")) {

                // Get the default sort order defined in the entity.
                $orderBy = $entity->getDefaultSortOrder();
            }
        }

        unset($queries['fields']);
        unset($queries['limit']);
        unset($queries['orderBy']);
        unset($queries['page']);

        $request->attributes->set(
            $configuration->getName(),
            new Collection($options['name'], $repository->findBy($queries, $orderBy, null, null))
        );

        return true;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        if ('collection_param_converter' !== $configuration->getConverter()) {
            return false;
        }

        return true;
    }
}
