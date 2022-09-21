# SimpleSAMLphp MemcacheMonitor module

![Build Status](https://github.com/simplesamlphp/simplesamlphp-module-memcachemonitor/workflows/CI/badge.svg?branch=master)
[![Coverage Status](https://codecov.io/gh/simplesamlphp/simplesamlphp-module-memcachemonitor/branch/master/graph/badge.svg)](https://codecov.io/gh/simplesamlphp/simplesamlphp-module-memcachemonitor)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/simplesamlphp/simplesamlphp-module-memcachemonitor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/simplesamlphp/simplesamlphp-module-memcachemonitor/?branch=master)
[![Type Coverage](https://shepherd.dev/github/simplesamlphp/simplesamlphp-module-memcachemonitor/coverage.svg)](https://shepherd.dev/github/simplesamlphp/simplesamlphp-module-memcachemonitor)
[![Psalm Level](https://shepherd.dev/github/simplesamlphp/simplesamlphp-module-memcachemonitor/level.svg)](https://shepherd.dev/github/simplesamlphp/simplesamlphp-module-memcachemonitor)

## Install

Install with composer

```bash
vendor/bin/composer require simplesamlphp/simplesamlphp-module-memcacheMonitor
```

## Configuration

Next thing you need to do is to enable the module:

in `config.php`, search for the `module.enable` key and set `memcacheMonitor` to true:

```php
'module.enable' => [ 'memcacheMonitor' => true, … ],
```
