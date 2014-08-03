<?php
namespace Senegal\Api\SdkBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class Controller extends BaseController{
    /**
     * @param $name
     *
     * @return \Pfd\Sdk\SdkInterface
     */
    protected function getSdk($name)
    {
        return $this->get('pfd.mediator.sdk')->getSdk($name);
    }
} 