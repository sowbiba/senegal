<?php

namespace Api\Sdk\Model;

/**
 * Class TimestampableInterface
 */
interface TimestampableInterface
{
    public function setCreatedAt($createdAt);

    public function setUpdatedAt($updatedAt);
}
