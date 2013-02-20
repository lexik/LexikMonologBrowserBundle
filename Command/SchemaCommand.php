<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Lexik\Bundle\LexikMonologDoctrineBundle\Model\SchemaBuilder;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class SchemaCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lexik:monolog-doctrine:schema-create')
            ->setDescription('Create schema to log monolog entries')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loggerClosure = function($message) use ($output) {
            $output->writeln($message);
        };

        $schemaBuilder = new SchemaBuilder(
            $this->getContainer()->get('lexik_monolog_doctrine.doctrine_dbal.connection'),
            $this->getContainer()->getParameter('lexik_monolog_doctrine.doctrine.table_name')
        );
        $schemaBuilder->create($loggerClosure);
    }
}
