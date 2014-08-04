<?php
namespace Api\Sdk\Bridge;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LegacyBridge
 * This class initalize sf1
 */
class LegacyBridge implements SdkBridge
{
    /**
     * @var string
     */
    protected $projectPath;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    public $moduleName;

    /**
     * @var string
     */
    public $actionName;

    /**
     * @var ContainerInterface
     */
    protected $container;

    const DEV_END  = "legacy_bridge_dev";
    const PROD_ENV = "legacy_bridge";

    /**
     * @param ContainerInterface $request
     * @param string             $path
     * @param string             $environment
     */
    public function __construct(ContainerInterface $container, $path, $environment = LegacyBridge::PROD_ENV)
    {
        $this->projectPath = $path;
        $this->environment = $environment;
        $this->container = $container;
    }

    public function handle()
    {
        $this->boot();
        \sfContext::getInstance()->getController()->dispatch();
        $this->shutdown();
    }

    /**
     * Given a callback function and its arguments, return the result of this function
     *
     * @param function $callback
     * @param array    $args     Arguments for the callback function
     *
     * @throws \Exception
     * @return type
     */
    public function permissiveTransaction($callback, $args = array(), $boot = true, $shutdown = true)
    {
        if( $boot ) {
            $this->boot();
        }

        if (null === $args) {
            $args = array();
        }

        if (!is_array($args)) {
            $args = array($args);
        }

        try {
            $result = call_user_func_array($callback, $args);
        } catch (\Exception $e) {
            $this->shutdown();

            throw $e;
        }

        if( $shutdown ) {
            $this->shutdown();
        }

        return $result;
    }

    /**
     * Bootstrap constants and loader for sf1
     */
    public function boot()
    {
        if (!defined('SF_ROOT_DIR')) {
            define('SF_ROOT_DIR', realpath($this->projectPath));
            define('SF_APP', 'back');

            $isDevEnv = $this->environment === "legacy_bridge_dev";
            define('SF_ENVIRONMENT', $this->environment);
            define('SF_DEBUG', $isDevEnv);
        }

        // This is needed for tests
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = $this->container->get('request')->getMethod();
        }

        require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        include_once(\sfConfigCache::getInstance()->checkConfig('config' . DIRECTORY_SEPARATOR . 'db_advanced_const.yml'));

        \sfContext::getInstance();
    }

    /**
     * Remove sf1 instance
     */
    public function shutdown()
    {
        if (class_exists('\sfContext') && \sfContext::hasInstance()) {

            $sfContextInstance = \sfContext::getInstance();

            $this->moduleName = $sfContextInstance->getModuleName();
            $this->actionName = $sfContextInstance->getActionName();
            $sfContextInstance->shutdown();
            \sfContext::removeInstance();
        }
    }
}
