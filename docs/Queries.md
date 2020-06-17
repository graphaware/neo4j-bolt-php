### Setting up a driver and creating a session

```php

use PTS\Bolt\GraphDatabase;

$driver = GraphDatabase::driver("bolt://localhost");
$session = $driver->session();
```

### Sending a Cypher statement

```php
$session = $driver->session();
$session->run("CREATE (n)");
$session->close();

// with parameters :

$session->run("CREATE (n) SET n += {props}", ['name' => 'Mike', 'age' => 27]);
```



### TLS Encryption

In order to enable TLS support, you need to set the configuration option to `REQUIRED`, here an example :

```php
$config = \PTS\Bolt\Configuration::newInstance()
    ->withCredentials('bolttest', 'L7n7SfTSj0e6U')
    ->withTLSMode(\PTS\Bolt\Configuration::TLSMODE_REQUIRED);

$driver = \PTS\Bolt\GraphDatabase::driver('bolt://hobomjfhocgbkeenl.dbs.graphenedb.com:24786', $config);
$session = $driver->session();
```
