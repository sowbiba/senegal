<?php

namespace Api\SdkBundle;

use Api\SdkBundle\DependencyInjection\CompilerPass\SdkEventListenersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ApiSdkBundle
 *
 * @package Api\SdkBundle
 */
class ApiSdkBundle extends Bundle
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
