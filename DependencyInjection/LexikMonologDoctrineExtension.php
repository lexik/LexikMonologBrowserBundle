<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LexikMonologDoctrineExtension extends Extension
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

        $container->setParameter('lexik_monolog_doctrine.base_layout', $config['base_layout']);

        $container->setParameter('lexik_monolog_doctrine.doctrine.table_name', $config['doctrine']['table_name']);
        $container->setParameter('lexik_monolog_doctrine.doctrine.connection.configuration', $config['doctrine']['connection']);
    }
}
