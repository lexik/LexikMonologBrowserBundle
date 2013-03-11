<?php

namespace Lexik\Bundle\MonologBrowserBundle\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;

use Doctrine\DBAL\Connection;

use Lexik\Bundle\MonologBrowserBundle\Processor\WebExtendedProcessor;
use Lexik\Bundle\MonologBrowserBundle\Formatter\NormalizerFormatter;

/**
 * Handler to send messages to a database through Doctrine DBAL.
 *
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class DoctrineDBALHandler extends AbstractProcessingHandler
{
    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * @param Connection $connection
     * @param string     $tableName
     * @param int        $level
     * @param string     $bubble
     */
    public function __construct(Connection $connection, $tableName, $level = Logger::DEBUG, $bubble = true)
    {
        $this->connection = $connection;
        $this->tableName  = $tableName;

        parent::__construct($level, $bubble);

        $this->pushProcessor(new WebProcessor());
        $this->pushProcessor(new WebExtendedProcessor());
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $record = $record['formatted'];

        try {
            $this->connection->insert($this->tableName, $record);
        } catch (\Exception $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter()
    {
        return new NormalizerFormatter();
    }
}
