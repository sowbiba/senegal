<?php

namespace Senegal\Api\SdkBundle;

use Senegal\Api\SdkBundle\DependencyInjection\CompilerPass\SdkEventListenersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SenegalApiSdkBundle
 *
 * @package Senegal\Api\SdkBundle
 */
class SenegalApiSdkBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SdkEventListenersPass());
    }
}
