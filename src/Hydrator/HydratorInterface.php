<?php

declare(strict_types=1);

namespace ReallyOrm\Hydrator;

use ReallyOrm\Entity\EntityInterface;

/**
 * Interface HydratorInterface.
 */
interface HydratorInterface
{
    /**
     * Receives a class name and an array of data in [entity_field_name => value] format
     * and returns a hydrated entity.
     *
     * @param string $className
     * @param array  $data
     *
     * @return EntityInterface
     */
    public function hydrate(string $className, array $data): EntityInterface;

    /**
     * Receives an entity and returns an array in [entity_field_name => value] format.
     *
     * @param EntityInterface $object
     *
     * @return array
     */
    public function extract(EntityInterface $object): array;

    /**
     * Sets the ID property on an entity.
     *
     * Use case:
     * - save() is called on a new entity.
     * - the entity's data is extracted and inserted into its corresponding database table.
     * - the database call returns the ID for the newly created row.
     * - the ID is added to the original entity.
     *
     * @param EntityInterface $entity
     * @param int             $id
     */
    public function hydrateId(EntityInterface $entity, int $id): void;
}
