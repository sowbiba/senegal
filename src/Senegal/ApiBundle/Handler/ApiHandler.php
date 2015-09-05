<?php
namespace Senegal\ApiBundle\Handler;

/**
 * Singleton used to call API
 */
class ApiHandler
{
    /**
     * @var ApiHandler|null
     */
    private static $_instance = null;

    /**
     * API host
     *
     * @var string
     */
    private static $host;

    /**
     * Curl resource
     *
     * @var resource
     */
    private $curl;

    /**
     * Call local config to get API host url and init Curl
     */
    public function __construct($localBaseUrl)
    {
        // Do ensure this URL ends with /
        self::$host = rtrim($localBaseUrl , '/') . '/';
        $this->initCurl();
    }

    /**
     * @return ApiHandler instance
     */
    public static function getInstance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new ApiHandler(self::$host);
        }

        return self::$_instance;
    }

    /**
     * Initialize a Curl resource
     */
    private function initCurl() {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        $this->curl = $curl;
    }

    /**
     * Call api to get a resource
     *
     * @param string $url Resource url
     *
     * @return string Resource
     *
     * @throws BadMethodCallException When $url is null
     */
    public function get($url){

        if(null === $url){
            throw new BadMethodCallException("url parameter can not be null");
        }

        curl_setopt($this->curl, CURLOPT_URL, self::$host . $url);
        $result = curl_errno($this->curl) ? false :curl_exec($this->curl);

        return $result;
    }

    /**
     * Close Curl resource
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }
}
