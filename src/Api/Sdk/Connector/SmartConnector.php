<?php
namespace Api\Sdk\Connector;

/**
 * This class determine wich connector to use
 *
 * Class SmartConnector
 * @package Api\Sdk\Connector
 * @author  Florent Coquel
 * @since   21/06/13
 *
 * Can't test it without a context (database)
 * @codeCoverageIgnore
 */
class SmartConnector extends AbstractConnector
{

    private $connectors = array();

    public function __construct($connectors)
    {
        $this->connectors = $connectors;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed|void
     * @throws NotImplementedException
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments = array())
    {
        foreach ($this->connectors as $connector) {
            try {
                if (method_exists($connector, $name)) {
                    $result  = call_user_func_array(array($connector, $name), $arguments);
                    $message = "Call method $name on " . get_class($connector);
                    $this->getLogger()->debug($message, $arguments);

                    return $result;
                }
            } catch (NotImplementedException $e) {
                continue;
            }
        }

        throw new \BadMethodCallException($name . ", you must be implement in a connector");
    }

}
