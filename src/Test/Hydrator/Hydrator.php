<?php

namespace ReallyOrm\Test\Hydrator;

use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Hydrator\HydratorInterface;
use ReallyOrm\Repository\RepositoryManagerInterface;

class Hydrator implements HydratorInterface
{
    /**
     * @var RepositoryManagerInterface
     */
    private $repoManager;

    /**
     * Hydrator constructor.
     * @param RepositoryManagerInterface $repoManager
     */
    public function __construct(RepositoryManagerInterface $repoManager)
    {
        $this->repoManager = $repoManager;
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function hydrate(string $className, array $data): EntityInterface
    {
        $reflect = new \ReflectionClass($className);
        /**
         * @var EntityInterface $object
         */
        $object = $reflect->newInstanceWithoutConstructor();
        $properties = $reflect->getProperties();
        foreach ($properties as $property) {
            preg_match('/@ORM\s(.*)$/m', $property->getDocComment(), $matches);
            if (!isset($matches[1])) {
                continue;
            }
            $property->setAccessible(true);
            $property->setValue($object, $data[$matches[1]]);
        }
        $this->repoManager->register($object);

        return $object;
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function extract(EntityInterface $object): array
    {
        $reflect = new \ReflectionClass($object);
        $properties = $reflect->getProperties();
        $data = [];
        foreach ($properties as $property) {
            preg_match('/@ORM\s(.*)$/m', $property->getDocComment(), $matches);
            if (!isset($matches[1])) {
                continue;
            }
            $property->setAccessible(true);
            $data[$matches[1]] = $property->getValue($object);
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function hydrateId(EntityInterface $entity, int $id): void
    {
        $reflect = new \ReflectionClass($entity);
        $properties = $reflect->getProperties();
        foreach ($properties as $property) {
            preg_match('/@Identifier\s(.*)$/m', $property->getDocComment(), $matches);
            if (!isset($matches[1])) {
                continue;
            }
            $property->setAccessible(true);
            if (strcmp($matches[1], 'id') === 0) {
                $property->setValue($entity, $id);
            }
        }
    }
}