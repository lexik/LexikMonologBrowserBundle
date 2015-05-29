<?php

namespace Lexik\Bundle\MonologBrowserBundle\Model;

use Doctrine\DBAL\Driver\Connection;

use Lexik\Bundle\MonologBrowserBundle\Model\Log;

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
                    ->select('l.channel, l.level, l.level_name, l.message, MAX(l.id) AS id, MAX(l.datetime) AS datetime, COUNT(l.id) AS count')
                    ->from($this->tableName, 'l')
                    ->groupBy('l.channel, l.level, l.level_name, l.message')
                    ->orderBy('datetime', 'DESC');
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

    /**
     * Retrieve similar logs of the given one.
     *
     * @param Log $log
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getSimilarLogsQueryBuilder(Log $log)
    {
        return $this->createQueryBuilder()
                    ->select('l.id, l.channel, l.level, l.level_name, l.message, l.datetime')
                    ->from($this->tableName, 'l')
                    ->andWhere('l.message = :message')
                    ->andWhere('l.channel = :channel')
                    ->andWhere('l.level = :level')
                    ->andWhere('l.id != :id')
                    ->setParameter(':message', $log->getMessage())
                    ->setParameter(':channel', $log->getChannel())
                    ->setParameter(':level', $log->getLevel())
                    ->setParameter(':id', $log->getId());
    }

    /**
     * Returns a array of levels with count entries used by logs.
     *
     * @return array
     */
    public function getLogsLevel()
    {
        $levels = $this->createQueryBuilder()
                       ->select('l.level, l.level_name, COUNT(l.id) AS count')
                       ->from($this->tableName, 'l')
                       ->groupBy('l.level, l.level_name')
                       ->orderBy('l.level', 'DESC')
                       ->execute()
                       ->fetchAll();

        $normalizedLevels = array();
        foreach ($levels as $level) {
            $normalizedLevels[$level['level']] = sprintf('%s (%s)', $level['level_name'], $level['count']);
        }

        return $normalizedLevels;
    }
}
