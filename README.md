## Neo4j Bolt PHP

PHP Driver for Neo4j's Bolt Remoting Protocol

[![Build Status](https://travis-ci.org/graphaware/neo4j-bolt-php.svg?branch=master)](https://travis-ci.org/graphaware/neo4j-bolt-php)

---

### DEV MODE

This library will remain in 1.0.0-dev version until stable release of Neo4j's Bolt.

---

### References :

* Documentation : http://remoting.neotechnology.com.s3-website-eu-west-1.amazonaws.com
* Python driver : https://github.com/neo4j/neo4j-python-driver
* Bolt How-To : https://github.com/nigelsmall/bolt-howto
* Java Driver : https://github.com/neo4j/neo4j-java-driver
* Neo4j 3.0 alpha : http://alpha.neotechnology.com.s3-website-eu-west-1.amazonaws.com/

### Requirements:

* PHP5.6+ (support for 5.5 soon)
* Neo4j3.0
* Raw sockets available

### Installation

#### Command line usage

[![Imgur](http://i.imgur.com/25gnaVk.png)]

Clone this repo and install the dependencies:

```bash
git clone git@github.com:graphaware/neo4j-bolt-php
cd neo4j-bolt-php
composer install
```

Issue queries with the `.bolt` bash script :

```bash
./bolt "MATCH (n:Movie) RETURN count(n)

cw@graphaware ~/d/g/neo4j-bolt-php> ./bolt "MATCH (n:Movie) RETURN count(n)"
+----------+
| count(n) |
+----------+
| 38       |
+----------+
```

You can also pass parameters along with your query :

```bash
./bolt "MATCH (n:Person) WHERE n.born = {year} RETURN n.name" -p year:1967

cw@graphaware ~/d/g/neo4j-bolt-php> ./bolt "MATCH (n:Person) WHERE n.born = {year} RETURN n.name" -p year:1967
+------------------------+
| n.name                 |
+------------------------+
| Carrie-Anne Moss       |
| Philip Seymour Hoffman |
| Julia Roberts          |
| James Marshall         |
| Andy Wachowski         |
| Ben Miles              |
| Steve Zahn             |
+------------------------+
```

### Embed in your application (not yet recommended)

Require the package in your composer dependencies :

```bash
composer require graphaware/neo4j-bolt-driver
```

Instantiate the driver by passing the host and the port to use, default port is `7687`.

```php
use GraphAware\Bolt\Driver;

$bolt = new Driver('localhost', 7687);
```

All statements need to be handled by a `Session`, getting a session is easy and will automatically trigger the
version negotiation (also called `Handshake`) :

```php
$session = $bolt->getSession();
```

Send your statements :

```php
$results = $session->run('MATCH (n:User {id: {id}}) RETURN n', array('id' => 123));
```

---

License : `MIT`

Author: `Christophe Willemsen`

---