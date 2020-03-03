<?php


namespace ReallyOrm\Test\Repository;


use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Repository\RepositoryInterface;
use ReallyOrm\Repository\RepositoryManagerInterface;

class RepositoryManager implements RepositoryManagerInterface
{
    private $repositories;

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
        //$res = $this->repositories[get_class($className)];
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