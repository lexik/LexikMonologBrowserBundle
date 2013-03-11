<?php

namespace Lexik\Bundle\MonologDoctrineBundle\Tests\DependencyInjection;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Lexik\Bundle\MonologDoctrineBundle\DependencyInjection\LexikMonologDoctrineExtension;

class LexikMonologDoctrineExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigLoad()
    {
        $extension = new LexikMonologDoctrineExtension();
        $extension->load(array($this->getConfig()), $container = new ContainerBuilder());

        // parameters
        $this->assertEquals('test_layout.html.twig', $container->getParameter('lexik_monolog_doctrine.base_layout'));
        $this->assertEquals('logs', $container->getParameter('lexik_monolog_doctrine.doctrine.table_name'));

        // services
        $this->assertTrue($container->hasDefinition('lexik_monolog_doctrine.doctrine_dbal.connection'));
        $this->assertTrue($container->hasDefinition('lexik_monolog_doctrine.handler.doctrine_dbal'));
    }

    protected function getConfig()
    {
        return array(
            'base_layout' => 'test_layout.html.twig',
            'doctrine'    => array(
                'table_name' => 'logs',
                'connection' => array(
                    'driver' => 'pdo_sqlite',
                    'dbname' => 'monolog',
                    'memory' => true,
                ),
            ),
        );
    }
}
