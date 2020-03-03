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
        // filters  = [field_name => value]
        // sorts = [field_name => direction]
        // $from = from offset
        // $size = to limit
        $sql = 'SELECT * FROM user WHERE ';
        foreach ($filters as $fieldName => $value) {
            $sql .= $fieldName . ' =:' . $fieldName;
            if (!end($filters)) {
                $sql .= ' AND ';
            }
        }
        $sql .= ' ORDER BY ';
        foreach ($sorts as $fieldName => $direction) {
            $sql .= ':'.$fieldName . ' ';
        }
        $sql .= ' LIMIT ' . $size . ' OFFSET ' . $from;
        $dbStmt = $this->pdo->prepare($sql);
        foreach ($filters as $fieldName => $value) {
            $dbStmt->bindParam(':' . $fieldName, $value);
        }
        foreach ($sorts as $fieldName => $direction){
            $dbStmt->bindParam(':'.$fieldName, $direction);
        }
        $dbStmt->execute();
        $array = $dbStmt->fetchAll();
        $objects = [];
        foreach ($array as $row){
           $objects[] = $this->hydrator->hydrate($this->entityName, $row);
        }
        return $objects;
    }



}