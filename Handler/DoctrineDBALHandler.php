<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Logger;

use Doctrine\DBAL\Connection;

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
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $record = $record['formatted'];
        unset($record['context'], $record['extra']);

        $this->connection->insert($this->tableName, $record);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter()
    {
        return new NormalizerFormatter();
    }
}
