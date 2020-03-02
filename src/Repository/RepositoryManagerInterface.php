<?php

declare(strict_types=1);

namespace ReallyOrm\Repository;

use ReallyOrm\Entity\EntityInterface;

/**
 * Interface RepositoryManagerInterface.
 *
 * Specifies methods to be implemented by a RepositoryManager.
 */
interface RepositoryManagerInterface
{
    /**
     * To implement Active Record, an entity has to perform CRUD operations.
     *
     * The actual database calls are the responsibility of its repository, which is provided by the repository manager.
     *
     * The calls will only be made after the entity is registered with the repository manager.
     *
     * @param EntityInterface $entity
     */
    public function register(EntityInterface $entity): void;

    /**
     * Returns the repository for an entity.
     *
     * @param string $className The entity's class name.
     *
     * @return RepositoryInterface
     */
    public function getRepository(string $className): RepositoryInterface;

    /**
     * Adds a repository to the manager's internal list of repositories.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryManagerInterface
     */
    public function addRepository(RepositoryInterface $repository): RepositoryManagerInterface;
}
