<?php

namespace ReallyOrm\Repository;

use PDO;
use phpDocumentor\Reflection\Types\This;
use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Hydrator\HydratorInterface;

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


    public function find(int $id): ?EntityInterface
    {
        $dbStmt = $this->pdo->prepare('SELECT * FROM ' . $this->getTableName() . ' WHERE id = :id');
        $dbStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $dbStmt->execute();
        $row = $dbStmt->fetch();

        return $this->hydrator->hydrate($this->entityName, $row);
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $filters): ?EntityInterface
    {
        // filters  = [field_name => value]
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ';
        foreach ($filters as $fieldName => $value) {
            $sql .= $fieldName . ' =:' . $fieldName;
            if (!end($filters)) {
                $sql .= ' AND ';
            }
        }
        $sql .= ' LIMIT 1';
        $dbStmt = $this->pdo->prepare($sql);
        foreach ($filters as $fieldName => $value) {
            $dbStmt->bindParam(':' . $fieldName, $value);
        }
        $dbStmt->execute();
        $row = $dbStmt->fetch();

        return $this->hydrator->hydrate($this->entityName, $row);
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
     * Returns the name of the associated table.
     *
     * @return string
     */
    public function getTableName(): string
    {
        //ReallyOrm\Test\Entity\User
        preg_match('/.*\\\\(.*)/', $this->entityName, $matches);

        return strtolower($matches[1]);
    }

    public function delete(EntityInterface $entity): bool
    {
        $data = $this->hydrator->extract($entity);
        $sql='DELETE FROM '.$this->getTableName().' WHERE id = :id';
        $dbStmt = $this->pdo->prepare($sql);
        $dbStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

        return $dbStmt->execute();
    }

}
