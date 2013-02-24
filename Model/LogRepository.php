<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Model;

use Doctrine\DBAL\Driver\Connection;

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

    public function getLogsQueryBuilder()
    {
        return $this->createQueryBuilder()
                    ->select('l.*')
                    ->from($this->tableName, 'l')
                    ->orderBy('l.id', 'DESC');
    }
}
