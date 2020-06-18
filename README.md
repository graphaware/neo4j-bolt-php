## Neo4j Bolt PHP

PHP low level Driver for Neo4j's binary Bolt Protocol

![build](https://github.com/PlumTreeSystems/neo4j-bolt-php/workflows/phpunit/badge.svg)

---

### About

Fork of no longer maintained [graphaware/neo4j-bolt-php](https://github.com/graphaware/neo4j-bolt-php) project.
This fork aims to maintain and update PHP Bolt driver to the newest version (V4). This driver is curently compatible with `graphaware/common` and can be used as drop in replacement to be used in [graphaware/neo4j-php-client](https://github.com/graphaware/neo4j-php-client), but eventually it will drop support for it.

### Supported versions

- Bolt V1 for Neo4j 3.0 to Neo4j 3.5
- Bolt V2 for Neo4j 3.4 to Neo4j 3.5
- Bolt V3 for Neo4j 3.5+
- Bolt V4 for Neo4j 4.0+

#### Structures
- `Point2D`
- `Point3D`
- `Duration`
-  `LocalDatetime` and `DateTime` (zoned and offset)
- `Time` and `LocalTime`
- `Duration`
- PHP's `\DateTime` (converts to neo4j's zoned `DateTime`)
- PHP's `\DateInterval` (converts to `Duration`)

### Requirements:

* PHP 7.2+
* Neo4j 3.0+
* PHP Sockets extension available
* `bcmath` extension
* `mbstring` extension

### Installation

Require the package in your dependencies :

```bash
composer require plumtreesystems/neo4j-bolt
```
### Usage
[Making queries](docs/Queries.md)

[Types](docs/Queries.md)

### TODO
- improve pipeline
- add support for async (ReactPHP)

#### Bug reports and Pull requests are welcome!

### Credits

Since Bolt V2, V3 and V4 protocols are undocumented, other official and unofficial drivers were used as a reference.
Big thanks goes to [bolt-rs](https://github.com/lucis-fluxum/bolt-rs) project.
