# Timer

## Example
See examples in `demo/timer.php`.

```PHP
// Set default options
Timer::$defaultOptions['getStats']['timestamp']      = false;
Timer::$defaultOptions['getStats']['nested']         = true;
Timer::$defaultOptions['getStats']['oneline']        = true;
Timer::$defaultOptions['getStats']['oneline_length'] = 50;

// Start "Total" timer
Timer::start('Total');

// No name test
Timer::start();
Timer::stop();

// Default timers
Timer::start('Sleep');
sleep(1);
Timer::stop();

// Loop timers
for ($i = 1; $i <= 2; $i++) {
    Timer::start('Loop level 1');
    for ($j = 1; $j <= 2; $j++) {
        Timer::start('Loop level 2');
        for ($k = 1; $k <= 2; $k++) {
            Timer::start('Loop level 3');

            Timer::stop();
        }
        Timer::stop();
    }
    Timer::stop();
}

// Callback timers
Timer::callback(null, 'strpos', 'Hello world', 'world');
Timer::callback(null, 'array_sum', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
Timer::callback(null, 'min', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
Timer::callback(null, 'max', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
Timer::callback(null, ['nonExistingClass', 'nonExistingMethod'], 'param1', 'param2');
Timer::callback(null, function () {
    return 'closure';
});

// Custom timers
Timer::custom('-5 minutes', strtotime('-5minutes'), time());
Timer::custom('+5 minutes', time(), strtotime('+5minutes'));

// Stop "Total" timer
Timer::stop();

// Show all timers
Timer::showAll();
```

Output
```
Invalid callback sent to Timer::callback:
Warning:  call_user_func_array() expects parameter 1 to be a valid callback, class 'nonExistingClass' not found in E:\GitHub\DebugTools\src\Timer.php on line 273

Total                                              |         1,002.02 ms.
|-- E:/GitHub/DebugTools/demo/index.php line 31    |             0.06 ms.
|-- Sleep                                          |         1,000.06 ms.
|-- Loop level 1 #1                                |             0.28 ms.
|-- |-- Loop level 2 #1                            |             0.11 ms.
|-- |-- |-- Loop level 3 #1                        |             0.02 ms.
|-- |-- |-- Loop level 3 #2                        |             0.03 ms.
|-- |-- Loop level 2 #2                            |             0.11 ms.
|-- |-- |-- Loop level 3 #3                        |             0.02 ms.
|-- |-- |-- Loop level 3 #4                        |             0.02 ms.
|-- Loop level 1 #2                                |             0.31 ms.
|-- |-- Loop level 2 #3                            |             0.11 ms.
|-- |-- |-- Loop level 3 #5                        |             0.03 ms.
|-- |-- |-- Loop level 3 #6                        |             0.03 ms.
|-- |-- Loop level 2 #4                            |             0.12 ms.
|-- |-- |-- Loop level 3 #7                        |             0.03 ms.
|-- |-- |-- Loop level 3 #8                        |             0.03 ms.
|-- custom: strpos                                 |             0.04 ms.
|-- custom: array_sum                              |             0.04 ms.
|-- custom: min                                    |             0.04 ms.
|-- custom: max                                    |             0.04 ms.
|-- custom: closure                                |             0.05 ms.
-5 minutes                                         |       300,000.00 ms.
+5 minutes                                         |       300,000.00 ms.
```
