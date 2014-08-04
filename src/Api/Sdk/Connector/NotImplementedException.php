<?php
/**
 * Author: Florent Coquel
 * Date: 20/05/13
 */

namespace Api\Sdk\Connector;

use Exception;

/**
 * This is the connectors exception
 *
 * Class NotImplementedException
 * @package Api\Sdk\Connector
 * @author Florent Coquel
 * @since 20/05/13
 * @codeCoverageIgnore
 */
class NotImplementedException extends Exception
{
    /**
     *
     * @param string    $methodName
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($methodName, $code = 0, Exception $previous = null)
    {
        $message = $methodName . " is not implemented";
        parent::__construct($message, $code, $previous);
    }

    /**
     * Custom message describing the object
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": {$this->message}\n";
    }

}
