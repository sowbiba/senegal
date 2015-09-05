<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;

/**
 * @codeCoverageIgnore
 */
abstract class ApiController extends FOSRestController
{
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
}
