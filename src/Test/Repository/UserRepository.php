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
     */
    public function find(int $id): ?EntityInterface
    {
        $dbStmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id');
        $dbStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $dbStmt->execute();

        $row = $dbStmt->fetch();
        $user = $this->hydrator->hydrate(User::class, $row);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $filters): ?EntityInterface
    {
        // TODO: Implement findOneBy() method.
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
    public function insertOnDuplicateKeyUpdate(EntityInterface $entity): bool
    {
        /*$dbStmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id');
        $dbStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $dbStmt->execute();
        $row = $dbStmt->fetch();


        $data = $hydrator->extract($user); // results in something like ['id' => 1, 'name' => 'Product ABC']
        // prepare statement and execute it. execution result will be a boolean.
        $this->hydrator->hydrateId($user, $this->pdo->lastInsertId());

        return $result;*/

    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $entity): bool
    {
        // TODO: Implement delete() method.
    }
}