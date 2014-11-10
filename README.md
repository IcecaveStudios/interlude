# Interlude

[![Build Status]](https://travis-ci.org/IcecaveStudios/interlude)
[![Test Coverage]](https://coveralls.io/r/IcecaveStudios/interlude?branch=develop)
[![SemVer]](http://semver.org)

**Interlude** is a small PHP library for repeating a non-blocking operation
until it succeeds, a timeout period is reached, or a maximum number of
attempts have been performed.

If you don't need the timeout feature, you might want to try [igorw/retry](https://github.com/igorw/retry).

* Install via [Composer](http://getcomposer.org) package [icecave/interlude](https://packagist.org/packages/icecave/interlude)
* Read the [API documentation](http://icecavestudios.github.io/interlude/artifacts/documentation/api/)

## Example

```php
use Icecave\Interlude\Exception\AttemptsExhaustedException;
use Icecave\Interlude\Exception\TimeoutException;
use Icecave\Interlude\Invoker;

$invoker = new Invoker;

$operation = function ($remainingTimeout, $remainingAttempts) {
    // do work ...
};

try {
    $invoker->invoke(
        $operation,
        10, // ten second timeout
        3   // maximum of three attempts
    );
} catch (TimeoutException $e) {
    echo 'The operation timed out!' . PHP_EOL;
} catch (AttemptsExhaustedException $e) {
    echo 'The operation was attempted the maximum number of times!' . PHP_EOL;
}
```

## Contact us

* Follow [@IcecaveStudios](https://twitter.com/IcecaveStudios) on Twitter
* Visit the [Icecave Studios website](http://icecave.com.au)
* Join `#icecave` on [irc.freenode.net](http://webchat.freenode.net?channels=icecave)

<!-- references -->
[Build Status]: http://img.shields.io/travis/IcecaveStudios/interlude/develop.svg?style=flat-square
[Test Coverage]: http://img.shields.io/coveralls/IcecaveStudios/interlude/develop.svg?style=flat-square
[SemVer]: http://img.shields.io/:semver-0.1.0-yellow.svg?style=flat-square
