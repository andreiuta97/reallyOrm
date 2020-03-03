<?php

use PHPUnit\Framework\TestCase;
use ReallyOrm\Test\Hydrator\Hydrator;
use ReallyOrm\Test\Entity\User;
use ReallyOrm\Test\Repository\RepositoryManager;
use ReallyOrm\Test\Repository\UserRepository;

/**
 * Class FunTest.
 *
 * Have fun!
 */
class FunTest extends TestCase
{
    private $pdo;

    /**
     * @var Hydrator
     */
    private $hydrator;

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var RepositoryManager
     */
    private $repoManager;

    protected function setUp(): void
    {
        parent::setUp();

        $config = require 'db_config.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
        $this->repoManager = new RepositoryManager();
        $this->hydrator = new Hydrator($this->repoManager);
        $this->userRepo = new UserRepository($this->pdo, User::class, $this->hydrator);
        $this->repoManager->addRepository($this->userRepo);
    }


    public function testCreateUser(): void
    {
        $user = new User();
        $user->setName('ciwawa');
        $user->setEmail('email');
        $this->repoManager->register($user);
        $result = $user->save();

        $this->assertEquals(true, $result);
        $this->assertNotEmpty($user->getId());
    }

    public function testUpdateUser(): void
    {
        $user = $this->userRepo->find(1);
        $user->setEmail('mail');
        //echo $user->getId();
        //echo $user->getEmail();

        $result = $user->save();

        $this->assertEquals(true, $result);
    }

    public function testFind(): void
    {
        /** @var User $user */
        $user = $this->userRepo->find(1);

        $this->assertEquals(1, $user->getId());
    }

    public function testFindOneBy(): void
    {
        /** @var User $user */
        $user = $this->userRepo->findOneBy(['email' => 'ana@gmail.com']);

        $this->assertEquals(2, $user->getId());
    }

    public function testFindBy(): void
    {
        /** @var User $user */
        $users = $this->userRepo->findBy(['name' => 'ciwawa'], ['email'=>'ASC'], 2,4);
        //var_dump($user);
        $this->assertEquals('ciwawa', $users[0]->getName());
    }


    public function testDelete(): void
    {
        /** @var User $user */
        $user = $this->userRepo->find(10);
        $delete = $this->userRepo->delete($user);

        $this->assertEquals(true, $delete);
    }
}
