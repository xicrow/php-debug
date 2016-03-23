# Debug
Debugging tools for PHP

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xicrow/debug/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xicrow/debug/?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/xicrow/debug/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/xicrow/debug/?branch=master)
[![Scrutinizer Build Status](https://scrutinizer-ci.com/g/xicrow/debug/badges/build.png?b=master)](https://scrutinizer-ci.com/g/xicrow/debug/build-status/master)

[![Packagist Latest Stable Version](https://poser.pugx.org/xicrow/debug/v/stable)](https://packagist.org/packages/xicrow/debug)
[![Packagist Total Downloads](https://poser.pugx.org/xicrow/debug/downloads)](https://packagist.org/packages/xicrow/debug)

## Installation
The recommended way to install is through [Composer](https://getcomposer.org/).
```JSON
{
    "require": {
        "xicrow/debug": "~1.0"
    }
}
```

## Example
See examples in the `demo` folder.

View the seperate readme for:
- [Debugger](Debugger.md)
- [Memory](Memory.md)
- [Timer](Timer.md)

## TODO
- ~~Debug functions for displaying variable information, pr(), vd(), etc.~~
	*Implemented, available in `Debugger` class*
- ~~Collection class for Timer::$timers, and maybe others~~
	*Implemented, available in `Collection` class*
- ~~Memory class for measuring memory usage~~
	*Implemented, available in `Memory` class*
- ~~Improve Timer and Memory, perhaps a Profiler class~~
	*Implemented, available in `Profiler` class*
- ~~Add PHPunit tests~~
	*Implemented, now remeber to keep them updated*
- Improve Debugger, foldable tree of arrays/objects

## License
Copyright &copy; 2016 Jan Ebsen
Licensed under the MIT license.
