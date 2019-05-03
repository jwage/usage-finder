# jwage/usage-finder

[![Build Status](https://travis-ci.org/jwage/usage-finder.svg)](https://travis-ci.org/jwage/usage-finder)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jwage/usage-finder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jwage/usage-finder/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jwage/usage-finder/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jwage/usage-finder/?branch=master)

The `jwage/usage-finder` library can be used to find usages of class methods in a code base. This is useful for
determining what libraries depend on a particular class and method.

## Install

You can install with composer:

    $ composer require jwage/usage-finder

## Console Usage

You can use the `vendor/bin/usage-finder` command line tool to execute your search:

    $ ./vendor/bin/usage-finder find /data/repositories/orm/ "Doctrine\Common\Collections\Collection::slice"
    Searching for Doctrine\Common\Collections\Collection::slice in /data/repositories/orm/.

      Found usage in lib/Doctrine/ORM/PersistentCollection.php on line 610.

    Finished in 7193.1ms

### Threads

You can run `usage-finder` with multiple threads by passing the `--threads` option:

    $ ./vendor/bin/usage-finder find /data/repositories/orm/ "Doctrine\Common\Collections\Collection::slice" --threads=2

## Manual Usage

You can manually use the tool as well if you want to integrate in to an existing tool:

```php
use UsageFinder\ClassMethodReference;
use UsageFinder\FindClassMethodUsages;

$classMethodReference = new ClassMethodReference('Doctrine\Common\Collections\Collection::slice');

$classMethodUsages = (new FindClassMethodUsages())->__invoke(
    '/data/repositories/orm/',
    $classMethodReference
);

foreach ($classMethodUsages as $classMethodUsage) {
    echo sprintf(
        'Found usage in <info>%s</info> on line <info>%d</info>.',
        $classMethodUsage->getFile(),
        $classMethodUsage->getLine()
    )."\n";
}
```
