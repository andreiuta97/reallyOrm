<?php


namespace ReallyOrm\Test\Repository;


use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Repository\RepositoryInterface;
use ReallyOrm\Repository\RepositoryManagerInterface;

class RepositoryManager implements RepositoryManagerInterface
{
    /**
     * @var array
     */
    private $repositories;

    /**
     * Adds each repository to the repositories array.
     *
     * RepositoryManager constructor.
     * @param array $repositories
     */
    public function __construct(array $repositories = [])
    {
        foreach ($repositories as $repository){
            $this->addRepository($repository);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(EntityInterface $entity): void
    {
        $entity->setRepositoryManager($this);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(string $className): RepositoryInterface
    {
        return $this->repositories[$className];
    }

    /**
     * @inheritDoc
     */
    public function addRepository(RepositoryInterface $repository): RepositoryManagerInterface
    {
        $this->repositories[$repository->getEntityName()] = $repository;

        return $this;
    }
}