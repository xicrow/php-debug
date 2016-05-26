# Timer

## Example
See example in [demo/timer.php](demo/timer.php).

Example output:
```
Total                                                                |            2.3298 MS
|-- debug/demo/timer.php line 17 > Xicrow\PhpDebug\Profiler::start() |            0.0458 MS
|-- Loop level 1 #1                                                  |            0.4129 MS
|-- |-- Loop level 2 #1                                              |            0.1552 MS
|-- |-- |-- Loop level 3 #1                                          |            0.0281 MS
|-- |-- |-- Loop level 3 #2                                          |            0.0381 MS
|-- |-- Loop level 2 #2                                              |            0.1700 MS
|-- |-- |-- Loop level 3 #3                                          |            0.0341 MS
|-- |-- |-- Loop level 3 #4                                          |            0.0339 MS
|-- Loop level 1 #2                                                  |            0.4389 MS
|-- |-- Loop level 2 #3                                              |            0.1659 MS
|-- |-- |-- Loop level 3 #5                                          |            0.0348 MS
|-- |-- |-- Loop level 3 #6                                          |            0.0360 MS
|-- |-- Loop level 2 #4                                              |            0.1719 MS
|-- |-- |-- Loop level 3 #7                                          |            0.0370 MS
|-- |-- |-- Loop level 3 #8                                          |            0.0370 MS
|-- callback: time                                                   |            0.0439 MS
|-- callback: strpos                                                 |            0.0451 MS
|-- callback: array_sum                                              |            0.0448 MS
|-- callback: array_rand                                             |            0.0429 MS
|-- callback: min                                                    |            0.0451 MS
|-- callback: max                                                    |            0.0491 MS
|-- callback: Xicrow\PhpDebug\Debugger::getDebugInformation          |            0.0930 MS
|-- callback: closure                                                |            0.0451 MS
|-- 5 seconds                                                        |            5.0000 S
|-- 5 minutes                                                        |            5.0000 M
|-- 5 hours                                                          |            5.0000 H
|-- 5 days                                                           |            5.0000 D
|-- 5 weeks                                                          |            5.0000 W
```
