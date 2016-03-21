# Debug
Debugging tools for PHP

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xicrow/Debug/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xicrow/Debug/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/xicrow/Debug/badges/build.png?b=master)](https://scrutinizer-ci.com/g/xicrow/Debug/build-status/master)

## Installation
The recommended way to install is though [Composer](https://getcomposer.org/).
```JSON
{
    "require": {
        "xicrow/Debug": "~1.0"
    }
}
```

## Example
See the examples in the `demo` folder.

## TODO
- ~~Debug functions for displaying variable information, pr(), vd(), etc.~~
	*Implemented, available in `Debugger` class*
- ~~Collection class for Timer::$timers, and maybe others~~
	*Implemented, available in `Collection` class*
- ~~Memory class for measuring memory usage~~
	*Implemented, available in `Memory` class*
- Improve Debugger, foldable tree of objects

## License
Copyright &copy; 2016 Jan Ebsen
Licensed under the MIT license.
