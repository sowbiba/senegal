<?php
namespace Api\SdkBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class Controller extends BaseController{
    /**
     * @param $name
     *
     * @return \Api\Sdk\SdkInterface
     */
    protected function getSdk($name)
    {
        return $this->get('api.mediator.sdk')->getSdk($name);
    }
} 