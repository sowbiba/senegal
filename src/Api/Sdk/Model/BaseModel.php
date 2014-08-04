<?php
/**
 * Model parent class
 *
 */
namespace Api\Sdk\Model;
use Api\Sdk\SdkInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Class BaseModel
 * @package Api\Sdk\Model
 * @ExclusionPolicy("all")
 */
abstract class BaseModel
{
    /**
     * @var \Api\Sdk\SdkInterface
     */
    protected $sdk;

    /**
     * @param SdkInterface $sdk
     * @param array        $properties
     */
    public function __construct(SdkInterface $sdk, array $properties = array())
    {
        $this->sdk = $sdk;
        $this->createFromArray($properties);
    }

    /**
     * Set values in properties of current instance.
     *
     * @param array $properties array of properties and values to set (@example : ['id' => 1, 'name' => 'Contract #1'])
     */
    public function createFromArray(array $properties)
    {
        foreach ($properties as $key => $value) {
            $setMethod = "set" . ucfirst($key);

            if (method_exists($this, $setMethod) && !is_null($value)) {
                $this->$setMethod($value);
            } elseif (method_exists($this, $key) && !is_null($value)) {
                $this->$key($value);
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $dataArray = array();

        foreach ($this as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $dataArray[$key] = $value;
        }

        return $dataArray;
    }

}
