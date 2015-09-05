# RedisCache

Redis cache middleware for Slim framework.

## Installation

    composer require abouvier/slim-redis-cache

## Usage

Cache every successful HTTP response for 8 hours in the local Redis server.

```php
$app = new \Slim\Slim();
// ...
$client = new \Predis\Client('tcp://localhost:6379', [
	'prefix' => $app->environment['SERVER_NAME']
]);
$app->add(new \Slim\Middleware\RedisCache($client, ['timeout' => 28800]));
// ...
$app->run();
```
