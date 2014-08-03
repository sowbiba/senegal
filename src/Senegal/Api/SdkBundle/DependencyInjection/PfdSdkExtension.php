<?php

namespace Senegal\Api\SdkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @codeCoverageIgnore
 */
class PfdSdkExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('pfd.observer.root_dir', $config['observer']['root_dir']);
        $container->setParameter('pfd.observer.web_path', $config['observer']['web_path']);
        $container->setParameter('pfd.observer.symfony_web_path', $config['observer']['symfony_web_path']);
        $container->setParameter('pfd.observer.environment', $config['observer']['environment']);
    }
}
