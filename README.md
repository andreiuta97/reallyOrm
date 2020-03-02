# reallyOrm
The interfaces of a basic ORM using a variation of Active Record.

The idea is to implement the given interfaces and extend the abstract classes to create a functional ORM.

For testing purposes, you are strongly encouraged to use PHPUnit. A simple context for this will be given, but you should add more testing cases and files.

Because PHPUnit is included in the composer dependency list, you can run the tests with the following command from the base directory of the repository:
`` vendor/bin/phpunit``

See PHPUnit docs for more information.


## Architectural Overview

There are three main parts of this library that you need to implement:
- the entities;
- the repositories;
- the hydrator. 

The entities must each have a corresponding repository, which they will use to:
- persist their information in the database;
- remove their information from the database;
- retrieve their information from the database.

In turn, the repositories must each use the hydrator to:
- extract entity information and build the corresponding query when inserting/updating table rows;
- translate the raw data into an entity when reading from the database.

A newly created entity is not registered with the repository manager. To enable your entity to interact with the database, the repository manager must become aware of it.

```php
$product = new Product(); // product is an AbstractEntity
$product->setName("Product ABC");

// assume you have an instance of RepositoryManager
$repoManager->register($product);
$product->save();
```

## Entities
These are the main actors of the ORM, as they are the core of your domain. You will model entities based on business requirements and needs.
They can be anything from User, Product to Image, Link. You will need to decide what parts of your business domain will be Entities and how to model them.

In your case, Entities are also a representation of a row in a database table. The implications of this are:
- you will need to create classes for each entity that your domain requires that extend the given `AbstractEntity`;
- you will also need to implement a way to map entity attributes to table columns. One possibility is to write annotations on each attribute (hint: https://www.php.net/manual/en/reflectionclass.getproperties.php; another hint: https://www.php.net/manual/en/reflectionproperty.getdoccomment.php).
Keep in mind that IDs will be auto-generated, so you will not be able to set the id of an entity (ideally it won't even have a setter), so you will need to take this into account when implementing the method that will insert the entity into the database. 


## Repository
The repositories will use:
 - an instance of PDO (https://www.php.net/manual/en/class.pdo.php) for CRUD operations;
 - the hydrator to convert the results of these operations to an object representation.


## Hydrator
The hydrator will be used by the repositories in the following way:
- for retrieval:
```php
# Product Repository context
 public function find(int $id) : ?EntityInterface
 {
    // prepare a statement that selects a row with the given id from the database (in associative mode!)
    $row = $dbStmt->fetch();
    $product = $this->hydrator->hydrate(Product::class, $row);
    
    return $product; // expected to be instance of Product
 }
```

- for insertion/update:
```php
# Product Repository context

 public function insertOnDuplicateKeyUpdate(EntityInterface $entity) : bool
 {
    $data = $hydrator->extract($product); // results in something like ['id' => 1, 'name' => 'Product ABC']
    
    // prepare statement and execute it. execution result will be a boolean.
    
    $this->hydrator->hydrateId($product, $this->pdo->lastInsertId());
    
    return $result;
}
```

## Misc
You will not have a lot of time to play with this tool, use it wisely. Think about the following project you will have to
use this tool on. How you can make it so it will help you the most, try to think how you will 
integrate it within the framework you built already. Use simple entities for this couple of days, like User and Quiz,
just to try and make a basic setup for what's coming next.
That being said, this is not the holy Grail of ORMs, if you think you have a good idea about how this lib can work
better, I'd love to hear/see it!


You can always come/write to me or Andra for any questions you might have about the assignment, or you know, just anything.
 
 
