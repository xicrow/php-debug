# Timer

## Example

See example in [demo/timer.php](demo/timer.php).

Example output:

```
php-debug/demo/timer.php line 70
Total                                                                                                |            0.8361 MS
|-- php-debug/demo/timer.php line 33                                                                 |            0.0679 MS
|-- Loop level 1 #1                                                                                  |            0.1490 MS
|-- |-- Loop level 2 #1                                                                              |            0.0598 MS
|-- |-- |-- Loop level 3 #1                                                                          |            0.0110 MS
|-- |-- |-- Loop level 3 #2                                                                          |            0.0260 MS
|-- |-- Loop level 2 #2                                                                              |            0.0660 MS
|-- |-- |-- Loop level 3 #3                                                                          |            0.0179 MS
|-- |-- |-- Loop level 3 #4                                                                          |            0.0181 MS
|-- Loop level 1 #2                                                                                  |            0.1490 MS
|-- |-- Loop level 2 #3                                                                              |            0.0620 MS
|-- |-- |-- Loop level 3 #5                                                                          |            0.0169 MS
|-- |-- |-- Loop level 3 #6                                                                          |            0.0169 MS
|-- |-- Loop level 2 #4                                                                              |            0.0610 MS
|-- |-- |-- Loop level 3 #7                                                                          |            0.0160 MS
|-- |-- |-- Loop level 3 #8                                                                          |            0.0169 MS
|-- callback: time                                                                                   |            0.0179 MS
|-- callback: strpos                                                                                 |            0.0150 MS
|-- callback: array_sum                                                                              |            0.0160 MS
|-- callback: array_rand                                                                             |            0.0160 MS
|-- callback: min                                                                                    |            0.0160 MS
|-- callback: max                                                                                    |            0.0150 MS
|-- callback: Xicrow\PhpDebug\Debugger::getDebugInformation                                          |            0.1040 MS
|-- callback: closure                                                                                |            0.0131 MS
|-- 5 seconds                                                                                        |         5000.0000 MS
|-- 5 minutes                                                                                        |       300000.0000 MS
|-- 5 hours                                                                                          |     18000000.0000 MS
|-- 5 days                                                                                           |    432000000.0000 MS
|-- 5 weeks                                                                                          |   3024000000.0000 MS
```
