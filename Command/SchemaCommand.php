<?php

namespace Lexik\Bundle\MonologBrowserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Lexik\Bundle\MonologBrowserBundle\Model\SchemaBuilder;

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
            ->setName('lexik:monolog-browser:schema-create')
            ->setDescription('Create schema to log Monolog entries')
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

        $tableName = $this->getContainer()->getParameter('lexik_monolog_browser.doctrine.table_name');

        $schemaBuilder = new SchemaBuilder(
            $this->getContainer()->get('lexik_monolog_browser.doctrine_dbal.connection'),
            $tableName
        );

        $error = false;
        try {
            $schemaBuilder->create($loggerClosure);
            $output->writeln(sprintf('<info>Created table <comment>%s</comment> for Doctrine Monolog connection</info>', $tableName));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Could not create table <comment>%s</comment> for Doctrine Monolog connection</error>', $tableName));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $error = true;
        }

        return $error ? 1 : 0;
    }
}
