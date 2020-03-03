<?php


namespace ReallyOrm\Test\Repository;


use PDO;
use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Repository\AbstractRepository;
use ReallyOrm\Test\Entity\User;

class UserRepository extends AbstractRepository
{
    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function insertOnDuplicateKeyUpdate(EntityInterface $entity): bool
    {
        $data = $this->hydrator->extract($entity);
        $sql = 'INSERT INTO user (id, name, email) VALUES (:id, :name, :email) ' .
            'ON DUPLICATE KEY UPDATE name = VALUES(name), email = VALUES(email)';
        $dbStmt = $this->pdo->prepare($sql);
        $dbStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $dbStmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $dbStmt->bindParam(':email', $data['email'], PDO::PARAM_STR);

        return $dbStmt->execute();
    }


    /**
     * @inheritDoc
     */
    public function findBy(array $filters, array $sorts, int $from, int $size): array
    {
        // TODO: Implement findBy() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $entity): bool
    {

    }
}