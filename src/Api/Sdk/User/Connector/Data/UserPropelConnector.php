<?php

namespace Api\Sdk\User\Connector\Data;

/**
 * This class is simply an alias of UserDataConnector.
 * Used because UserConnector::getById for to use the propel connector which is illegal in tests
 */
class UserPropelConnector extends UserDataConnector
{
}
