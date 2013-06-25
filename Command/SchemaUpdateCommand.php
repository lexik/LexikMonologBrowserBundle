<?php

namespace Lexik\Bundle\MonologBrowserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Lexik\Bundle\MonologBrowserBundle\Model\SchemaBuilder;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class SchemaUpdateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lexik:monolog-browser:schema-update')
            ->setDescription('Update Monolog table from schema')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getContainer()->get('lexik_monolog_browser.doctrine_dbal.connection');
        $tableName = $this->getContainer()->getParameter('lexik_monolog_browser.doctrine.table_name');

        $schemaBuilder = new SchemaBuilder($connection, $tableName);

        $sqls = $schemaBuilder->getSchemaDiff()->toSql($connection->getDatabasePlatform());

        if (0 == count($sqls)) {
            $output->writeln('Nothing to update - your database is already in sync with the current Monolog schema.');

            return;
        }

        $output->writeln('<comment>ATTENTION</comment>: This operation may not be executed in a production environment, use Doctrine Migrations instead.');
        $output->writeln(sprintf('<info>SQL operations to execute to Monolog table "<comment>%s</comment>":</info>', $tableName));
        $output->writeln(implode(';' . PHP_EOL, $sqls));

        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation(
                $output,
                '<question>Do you want to execute these SQL operations?</question>',
                false
            )) {
            return;
        }

        $error = false;
        try {
            $schemaBuilder->update();
            $output->writeln(sprintf('<info>Successfully updated Monolog table "<comment>%s</comment>"! "%s" queries were executed</info>', $tableName, count($sqls)));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Could not update Monolog table "<comment>%s</comment>"...</error>', $tableName));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $error = true;
        }

        return $error ? 1 : 0;
    }
}
