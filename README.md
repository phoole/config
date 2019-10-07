# config
[![Build Status](https://travis-ci.com/phoole/config.svg?branch=master)](https://travis-ci.com/phoole/config)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phoole/config/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phoole/config/?branch=master)
[![Code Climate](https://codeclimate.com/github/phoole/config/badges/gpa.svg)](https://codeclimate.com/github/phoole/config)
[![PHP 7](https://img.shields.io/packagist/php-v/phoole/config)](https://packagist.org/packages/phoole/config)
[![Latest Stable Version](https://img.shields.io/github/v/release/phoole/config)](https://packagist.org/packages/phoole/config)
[![License](https://img.shields.io/github/license/phoole/config)]()

A slim configuration loader lib using PHP, JSON, YAML files, easy to use, yet powerful
configuration management library for PHP. It requires PHP 7.2+ and is compliant with
[PSR-1][PSR-1], [PSR-4][PSR-4], [PSR-12][PSR-12].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"
[PSR-12]: http://www.php-fig.org/psr/psr-2/ "PSR-12: Extended Coding Style Guide"

Installation
---
Install via the `composer` utility.

```
composer require "phoole/config=1.*"
```

or add the following lines to your `composer.json`

```json
{
    "require": {
       "phoole/config": "1.*"
    }
}
```

Features
---

- Simple interface, `get($id)`, `has($id)`, `with($id, $value)`.

- One central place for all config files for ease of management.

  ```
  config/
   |
   |___ production/
   |       |
   |       |___ host1/
   |       |      |___ db.php
   |       |      |___ redis.php
   |       |
   |       |___ db.php
   |
   |___ dev/
   |     |
   |     |___ redis.php
   |     |___ db.php
   |
   |___ db.php
   |___ redis.php
   |___ system.php
  ```

- May use an [environment](#env) value, such as `production` or `production/host1`
  for switching between different configurations.

- Use of [references](#ref) in configuration value is fully supported, such as
  `${system.tmpdir}`.

- Hierachy configuration structure with dot notation like `db.auth.host`.

- [Array access](#array) for ease of use. e.g. `$user = $config['db.user'];`.

- Support `.php`, `.json`, `.yml`(need yaml extension installed) type of config
  files.

Usage
---

- <a name="env"></a>Use environment value

  Usually application running environment is different on different servers. A
  good practice is setting environment in a `.env` file somewhere on the host,
  and put all configuration files in one central `config/` directory.

  A sample `.env` file,

  ```shell
  # installation base
  BASE_DIR=/www

  # app directory
  APP_DIR=${BASE_DIR}/app

  # config directory
  CONFIG_DIR=${APP_DIR}/config

  # app env for current host
  APP_ENV=production/host1
  ```

  In a sample `bootstrap.php` file,

  ```php
  use Phoole\Config\Config;
  use Phoole\Env\Environment;

  // load server environment from '.env' file
  (new Environment())->load(__DIR__ . '/.env');

  // create config instance with the config file loader
  $config = new Config(getenv('CONFIG_DIR'), getenv('APP_ENV'));

  // object access of $config
  $db_config = $config->get('db');

  // array access of $config
  $db_config = $config['db'];
  ```

- <a name="group"></a>Central config directory and configuration grouping

  - Configuration grouping

    Configurations are gathered into one directory and are grouped into files
    and subdirectories for ease of management.

    For example, the `config/system.php` holds `system.*` configurations

    ```php
    // system.php
    return [
        'tmpdir' => '/usr/local/tmp',
        // ...
    ];
    ```

    Later, `system` related configs can be retrieved as

    ```php
    // array access of $config
    $dir = $config['system.tmpdir'];
    ```

    Or being used in other configs as [references](#ref).

  - Configuration files loading order

    If the environment is set to `production/host1`, the config files loading
    order are (assume config files are `*.php`),

    1. `config/config/*.php`

    2. `config/production/*.php`

    3. `config/production/host1/*.php`

    Configuration values are overwritten and replaced those from later loaded
    files.

- <a name="ref"></a>Use of references

  References make your configuration easy to manage.

  For example, in the `system.php`

  ```php
  return [
      'tmpdir' => '/var/local/tmp',
      ...
  ];
  ```

  In your `cache.php` file,

  ```php
  return [
      // a local filesystem cache driver
      'local' => [
          'driver' => 'filesystem',
          'params' => [
              'root_dir'   => '${system.tmpdir}/cache', // use reference here
              'hash_level' => 2
          ]
      ],
      ...
  ];
  ```

  You may reset the reference start and ending matching pattern as follows,

  ```php
  // now reference is something like '%{system.tmpdir}%'
  $config->setReferencePattern('%{', '}%');
  ```

- <a name="array"></a>ArrayAccess and DOT notation

  `Config` class implements `ArrayAccess` interface. So config values can be
  accessed just like an array.

  ```php
  // test
  if (!isset($config['db.auth.user'])) {
      $user = $config['db.auth.user'];
  }
  ```

  Hierachy configuration structure with dot notation like `db.auth.host`.

  ```php
  // returns the db config array
  $db_config = $config->get('db');

  // returns a string
  $db_host = $config->get('db.auth.host');
  ```

  Both flat notation and array notation are supported and can co-exist at the
  same time.

  ```php
  // db config file
  return [
      // array notation
      'auth' => [
          'host' => 'localhost',
          'port' => 3306
      ],

      // flat notation
      'auth.user' => 'dbuser'
  ];
  ```

- <a name="immutable"></a>After initial loading, `$config` is immutable. If you want to
  add new conf values. You may use,

  ```php
  $newconf = $config->with('redis', ['host' => 'localhost']);
  ```
  where `$newconf` is a new configuration object.


APIs
---

- <a name="api_1"></a>`ConfigInterface` API

  - `get(string $id): mixed`

    The return value might be a `string`, `array`, or even `object`. Returns `null`
    if not found.

  - `has(string $id): bool`

    Test if `$id` exists or not. Returns a `boolean` value.

  - `with(string $id, mixed $value): Config`

    Returns a new config object with `$id` set.

- <a name="api_2"></a>`ReferenceInterface` API

  - `setReferencePattern(string $start, string $end): $this`

    Reset the reference start chars and ending chars. The default are `'${'` and
    `'}'`

Testing
---

```bash
$ composer test
```

Dependencies
---

- PHP >= 7.2.0

License
---

- [Apache 2.0](https://www.apache.org/licenses/LICENSE-2.0)
