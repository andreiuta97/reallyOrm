<?php

declare(strict_types=1);

namespace ReallyOrm\Entity;

use ReallyOrm\Repository\RepositoryManagerInterface;
use ReallyOrm\Repository\RepositoryInterface;

/**
 * Class AbstractEntity.
 *
 * Intended as a parent for concrete entities.
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var RepositoryManagerInterface
     */
    protected $repositoryManager;

    /**
     * @param RepositoryManagerInterface $repositoryManager
     *
     * @return EntityInterface
     */
    public function setRepositoryManager(RepositoryManagerInterface $repositoryManager): EntityInterface
    {
        $this->repositoryManager = $repositoryManager;

        return $this;
    }

    /**
     * Returns the repository for the current entity.
     *
     * @return RepositoryInterface
     */
    protected function getRepository(): RepositoryInterface
    {
        return $this->repositoryManager->getRepository(get_class($this));
    }

    /**
     * Saves the current entity.
     *
     * A database call can be executed only after the entity is registered  with the repository manager.
     *
     * @return bool
     */
    public function save(): bool
    {
        if ($this->repositoryManager == null) {
            return false;
        }

        return $this->getRepository()->insertOnDuplicateKeyUpdate($this);
    }

    /**
     * Deletes the current entity.
     *
     * A database call can be executed only after the entity is registered  with the repository manager.
     *
     * @return bool
     */
    public function remove(): bool
    {
        if ($this->repositoryManager == null) {
            return false;
        }

        $this->getRepository()->delete($this);
    }
}
