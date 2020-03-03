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
        $sql = 'INSERT INTO user (name, email) VALUES (:name, :email) ' .
            'ON DUPLICATE KEY UPDATE name = VALUES(name), email = VALUES(email)';
        $dbStmt = $this->pdo->prepare($sql);
        //$dbStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $dbStmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $dbStmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $result = $dbStmt->execute();
        $idVer= $this->pdo->lastInsertId();
        $this->hydrator->hydrateId($entity, $idVer);
        return $result;
    }

}