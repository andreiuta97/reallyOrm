<?php

declare(strict_types=1);

namespace ReallyOrm\Repository;

use ReallyOrm\Criteria\Criteria;
use ReallyOrm\Entity\EntityInterface;

/**
 * Interface RepositoryInterface.
 *
 * To be implemented by concrete repositories.
 */
interface RepositoryInterface
{
    /**
     * Returns one entity with the given ID or null in case the entity doesn't exist.
     *
     * @param int $id
     *
     * @return EntityInterface
     */
    public function find(int $id): ?EntityInterface;

    /**
     * Returns one entity filtered by the given criteria.
     *
     * @param array $filters Format [field_name => value]
     *
     * @return EntityInterface
     */
    public function findOneBy(array $filters) : ?EntityInterface;

    /**
     * Returns a filtered, sorted, and paginated set of entities.
     *
     * @param Criteria $criteria
     *
     * @return EntityInterface[]
     */
    public function findBy(Criteria $criteria): array;

    /**
     * Inserts new entity or updates existing entity if a duplicate error occurs.
     *
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function insertOnDuplicateKeyUpdate(EntityInterface $entity) : bool;

    /**
     * Deletes the given entity and returns true if successful.
     *
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function delete(EntityInterface $entity) : bool;

    /**
     * Returns the name of the entity associated with the repository.
     *
     * @return string
     */
    public function getEntityName(): string;

    public function getTableName(): string;
}
