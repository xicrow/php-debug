# Debug
Debugging tools for PHP

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xicrow/php-debug/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xicrow/php-debug/?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/xicrow/php-debug/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/xicrow/php-debug/?branch=master)
[![Scrutinizer Build Status](https://scrutinizer-ci.com/g/xicrow/php-debug/badges/build.png?b=master)](https://scrutinizer-ci.com/g/xicrow/php-debug/build-status/master)

[![Packagist Latest Stable Version](https://poser.pugx.org/xicrow/php-debug/v/stable)](https://packagist.org/packages/xicrow/php-debug)
[![Packagist Total Downloads](https://poser.pugx.org/xicrow/php-debug/downloads)](https://packagist.org/packages/xicrow/php-debug)

## Installation
The recommended way to install is through [Composer](https://getcomposer.org/):
```
composer require xicrow/php-debug:~3.0
```

Optionally add it to your `composer.json` file:
```
{
    "require": {
        "xicrow/php-debug": "~3.0"
    }
}
```

## Example
See examples in the `demo` folder.

View the seperate readme for:
- [Debugger](Debugger.md)
- [Timer](Timer.md)

## TODO
- ~~Debug functions for displaying variable information, pr(), vd(), etc.~~
	*Implemented, available in `Debugger` class*
- ~~Collection class for Timer::$timers, and maybe others~~
	*Implemented, available in `Collection` class*
    *Removed, too memory hungry*
- ~~Memory class for measuring memory usage~~
	*Implemented, available in `Memory` class*
    *Removed, too unreliable*
- ~~Improve Timer and Memory, perhaps a Profiler class~~
	*Implemented, available in `Profiler` class*
    *Removed, along with Memory*
- ~~Add PHPunit tests~~
	*Implemented, now remeber to keep them updated*
- Improve Debugger, foldable tree of arrays/objects
- Groupable timers, timers with the same name will be grouped and min, max and average will be calculated (activate when stopping a timer)
- Update example demo output in Debugger and Timer README
- Update to PHP 7.0

## License
Copyright &copy; 2018 Jan Ebsen
Licensed under the MIT license.
