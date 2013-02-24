<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Model;

use Doctrine\DBAL\Driver\Connection;

use Lexik\Bundle\LexikMonologDoctrineBundle\Model\Log;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class LogRepository
{
    /**
     * @var Connection $conn
     */
    protected $conn;

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * @param Connection $conn
     * @param string     $tableName
     */
    public function __construct(Connection $conn, $tableName)
    {
        $this->conn      = $conn;
        $this->tableName = $tableName;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->conn->createQueryBuilder();
    }

    /**
     * Initialize a QueryBuilder of latest log entries.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getLogsQueryBuilder()
    {
        return $this->createQueryBuilder()
                    ->select('l.id, l.channel, l.level, l.level_name, l.message, l.datetime')
                    ->from($this->tableName, 'l')
                    ->orderBy('l.id', 'DESC');
    }

    /**
     * Retrieve a log entry by his ID.
     *
     * @param integer $id
     *
     * @return Log|null
     */
    public function getLogById($id)
    {
        $log = $this->createQueryBuilder()
                    ->select('l.*')
                    ->from($this->tableName, 'l')
                    ->where('l.id = :id')
                    ->setParameter(':id', $id)
                    ->execute()
                    ->fetch();

        if (false !== $log) {
            return new Log($log);
        }
    }
}
