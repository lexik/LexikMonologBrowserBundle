<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Model;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Schema\Schema;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class SchemaBuilder
{
    /**
     * @var Connection $conn
     */
    protected $conn;

    /**
     * @var Schema $schema
     */
    protected $schema;

    public function __construct(Connection $conn, $tableName)
    {
        $this->conn = $conn;

        $this->schema = new Schema();

        $entryTable = $this->schema->createTable($tableName);
        $entryTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $entryTable->addColumn('channel', 'string', array('length' => 255, 'notNull' => true));
        $entryTable->addColumn('level', 'integer', array('notNull' => true));
        $entryTable->addColumn('level_name', 'string', array('length' => 255, 'notNull' => true));
        $entryTable->addColumn('message', 'text', array('notNull' => true));
        $entryTable->addColumn('datetime', 'datetime', array('notNull' => true));
        $entryTable->setPrimaryKey(array('id'));
    }

    public function create(\Closure $logger)
    {
        $this->conn->beginTransaction();

        try {
            $queries = $this->schema->toSql($this->conn->getDatabasePlatform());
            foreach ($queries as $query) {
                $logger($query);
                $this->conn->query($query);
            }

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
}
