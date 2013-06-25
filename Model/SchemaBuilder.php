<?php

namespace Lexik\Bundle\MonologBrowserBundle\Model;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Comparator;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class SchemaBuilder
{
    /**
     * @var Connection $conn
     */
    protected $conn;

    protected $tableName;

    /**
     * @var Schema $schema
     */
    protected $schema;

    public function __construct(Connection $conn, $tableName)
    {
        $this->conn      = $conn;
        $this->tableName = $tableName;

        $this->schema = new Schema();

        $entryTable = $this->schema->createTable($this->tableName);
        $entryTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $entryTable->addColumn('channel', 'string', array('length' => 255, 'notNull' => true));
        $entryTable->addColumn('level', 'integer', array('notNull' => true));
        $entryTable->addColumn('level_name', 'string', array('length' => 255, 'notNull' => true));
        $entryTable->addColumn('message', 'text', array('notNull' => true));
        $entryTable->addColumn('datetime', 'datetime', array('notNull' => true));
        $entryTable->addColumn('context', 'text');
        $entryTable->addColumn('extra', 'text');
        $entryTable->addColumn('http_server', 'text');
        $entryTable->addColumn('http_post', 'text');
        $entryTable->addColumn('http_get', 'text');
        $entryTable->setPrimaryKey(array('id'));
    }

    public function create(\Closure $logger = null)
    {
        $queries = $this->schema->toSql($this->conn->getDatabasePlatform());

        $this->executeQueries($queries, $logger);
    }

    public function update(\Closure $logger = null)
    {
        $queries = $this->getSchemaDiff()->toSaveSql($this->conn->getDatabasePlatform());

        $this->executeQueries($queries, $logger);
    }

    public function getSchemaDiff()
    {
        $diff = new SchemaDiff();
        $comparator = new Comparator();

        $tableDiff = $comparator->diffTable(
            $this->conn->getSchemaManager()->createSchema()->getTable($this->tableName),
            $this->schema->getTable($this->tableName)
        );

        if (false !== $tableDiff) {
            $diff->changedTables[$this->tableName] = $tableDiff;
        }

        return $diff;
    }

    protected function executeQueries(array $queries, \Closure $logger = null)
    {
        $this->conn->beginTransaction();

        try {
            foreach ($queries as $query) {
                if (null !== $logger) {
                    $logger($query);
                }

                $this->conn->query($query);
            }

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
}
