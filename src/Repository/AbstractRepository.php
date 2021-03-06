<?php

namespace ReallyOrm\Repository;

use PDO;
use phpDocumentor\Reflection\Types\This;
use ReallyOrm\Criteria\Criteria;
use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Hydrator\HydratorInterface;
use ReallyOrm\SearchResult\SearchResult;

/**
 * Class AbstractRepository.
 *
 * Intended as a parent for entity repositories.
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * Represents a connection between PHP and a database server.
     *
     * https://www.php.net/manual/en/class.pdo.php
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * The name of the entity associated with the repository.
     *
     * This could be used, for example, to infer the underlying table name.
     *
     * @var string
     */
    protected $entityName;

    /**
     * The hydrator is used in the following two cases:
     * - build an entity from a database row
     * - extract entity fields into an array representation that is easier to use when building insert/update statements.
     *
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * AbstractRepository constructor.
     *
     * @param \PDO $pdo
     * @param string $entityName
     * @param HydratorInterface $hydrator
     */
    public function __construct(PDO $pdo, string $entityName, HydratorInterface $hydrator)
    {
        $this->pdo = $pdo;
        $this->entityName = $entityName;
        $this->hydrator = $hydrator;
    }

    /**
     * @param int $id
     * @return EntityInterface|null
     */
    public function find(int $id): ?EntityInterface
    {
        $dbStmt = $this->pdo->prepare('SELECT * FROM ' . $this->getTableName() . ' WHERE id = :id');
        $dbStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $dbStmt->execute();
        $row = $dbStmt->fetch();
        $entity = $this->hydrator->hydrate($this->entityName, $row);
        $this->hydrator->hydrateId($entity, $row['id']);

        return $entity;
    }

    /**
     * @param array $filters
     *
     * @return EntityInterface|null
     */
    public function findOneBy(array $filters): ?EntityInterface
    {
        // filters  = [field_name => value]
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ';
        foreach ($filters as $fieldName => $value) {
            $sql .= $fieldName . ' = :' . $fieldName . ' AND ';
        }
        $sql = substr($sql, 0, -5);
        $sql .= ' LIMIT 1';

        $dbStmt = $this->pdo->prepare($sql);
        foreach ($filters as $fieldName => &$value) {
            $dbStmt->bindParam(':' . $fieldName, $value);
        }
        $dbStmt->execute();
        $row = $dbStmt->fetch();
        $entity = $this->hydrator->hydrate($this->entityName, $row);
        $this->hydrator->hydrateId($entity, $row['id']);

        return $entity;
    }

    /**
     * @param Criteria $criteria
     * @return SearchResult
     */
    public function findBy(Criteria $criteria): SearchResult
    {
        // filters  = [field_name => value]
        // sorts = [field_name => direction]
        // $from = from offset
        // $size = to limit
        $sql = 'SELECT * FROM ' . $this->getTableName();

        $sql .= $criteria->toQuery();

        $dbStmt = $this->pdo->prepare($sql);
        $criteria->bindParamsToStatement($dbStmt);
        $dbStmt->execute();
        $array = $dbStmt->fetchAll();
        $objects = [];
        foreach ($array as $row) {
            $object = $this->hydrator->hydrate($this->entityName, $row);
            $this->hydrator->hydrateId($object, $row['id']);
            $objects[] = $object;
        }
        $searchResult = new SearchResult();
        $searchResult->setItems($objects);
        $searchResult->setTotalCount($this->getNumberOfObjects($criteria));

        return $searchResult;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function insertOnDuplicateKeyUpdate(EntityInterface $entity): bool
    {
        $data = $this->hydrator->extract($entity);

        // remove null ID values
        if (!isset($data['id'])) {
            unset($data['id']);
        }

        $columns = implode(", ", array_keys($data));
        $values = implode(", :", array_keys($data));
        $sql = 'INSERT INTO ' . $this->getTableName() . ' (' . $columns . ') VALUES (:'
            . $values . ') ON DUPLICATE KEY UPDATE ';
        foreach (array_keys($data) as $dataKey) {
            $sql .= $dataKey . ' = VALUES(' . $dataKey . '), ';
        }

        $sql = substr($sql, 0, -2);
        $dbStmt = $this->pdo->prepare($sql);
        foreach ($data as $columnName => &$value) {
            if (is_bool($value)) {
                $dbStmt->bindValue(':' . $columnName, $value, PDO::PARAM_BOOL);
                continue;
            }
            $dbStmt->bindParam(':' . $columnName, $value);
        }
        $result = $dbStmt->execute();
        if ($this->pdo->lastInsertId() != 0) {
            $this->hydrator->hydrateId($entity, $this->pdo->lastInsertId());
        }

        return $result;
    }

    /**
     * @param Criteria $criteria
     * @return array
     */
    /**
     * @param Criteria $criteria
     * @return array
     */
    public function findBySearch(Criteria $criteria): SearchResult
    {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' ';
        $sql .= $criteria->toQuerySearch();
        $dbStmt = $this->pdo->prepare($sql);
        $criteria->bindValueToStatementSearch($dbStmt);
        $dbStmt->execute();
        $array = $dbStmt->fetchAll();
        $objects = [];
        foreach ($array as $row) {
            $object = $this->hydrator->hydrate($this->entityName, $row);
            $this->hydrator->hydrateId($object, $row['id']);
            $objects[] = $object;
        }

        $searchResult = new SearchResult();
        $searchResult->setItems($objects);
        $searchResult->setTotalCount($this->getNumberOfObjects($criteria));

        return $searchResult;
    }

    /**
     * Returns the number of objects from a table.
     *
     * @param Criteria $criteria
     * @return int
     */
    public function getNumberOfObjects(Criteria $criteria): int
    {
        $sql = 'SELECT count(*) as objectsNumber FROM ' . $this->getTableName() . ' ';
        $sql .= $criteria->toQueryCount();
        $dbStmt = $this->pdo->prepare($sql);
        $criteria->bindValueToStatementSearch($dbStmt);
        $dbStmt->execute();

        return $dbStmt->fetch(\PDO::FETCH_COLUMN);
    }


    /**
     * Returns the name of the associated entity.
     *
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * Returns the name of the associated table from the full entity name.
     *
     * Examples: entityName = QuizApp\Entity\User
     *           output: user
     *
     *           entityName = QuizApp\Entity\QuizTemplate
     *           output: quiz_template
     *
     * @return string
     */
    public function getTableName(): string
    {
        $parts = explode('\\', $this->entityName);
        $tableName = end($parts);
        $tableName = lcfirst($tableName);
        if (ctype_lower($tableName)) {
            return $tableName;
        }
        $pieces = preg_split('/(?=[A-Z])/', $tableName);
        $tableName = $pieces[0] . '_' . lcfirst($pieces[1]);

        return $tableName;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool
    {
        $data = $this->hydrator->extract($entity);
        $sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE id = :id';
        $dbStmt = $this->pdo->prepare($sql);
        $dbStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

        return $dbStmt->execute();
    }

}
