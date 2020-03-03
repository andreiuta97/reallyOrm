<?php

namespace ReallyOrm\Test\Hydrator;

use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Hydrator\HydratorInterface;

class Hydrator implements HydratorInterface
{
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
        //get props from reflection
        $properties = $reflect->getProperties();
        //foreach prop get doc block (orm)
        foreach ($properties as $property) {
            preg_match('/@ORM\s(.*)$/m', $property->getDocComment(), $matches);
            if (!isset($matches[1])) {
                continue;
            }
            //set property accessible
            $property->setAccessible(true);
            //set value din data
            $property->setValue($object, $data[$matches[1]]);
        }

        return $object;
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function extract(EntityInterface $object): array
    {
        $reflect = new \ReflectionClass($object);
        //get props from reflection
        $properties = $reflect->getProperties();
        $data = [];
        //foreach prop get doc block (orm)
        foreach ($properties as $property) {
            preg_match('/@ORM\s(.*)$/m', $property->getDocComment(), $matches);
            if (!isset($matches[1])) {
                continue;
            }
            //set property accessible
            $property->setAccessible(true);
            //set value din data
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