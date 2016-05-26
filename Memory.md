# Memory

## Example
See example in [demo/memory.php](demo/memory.php).

Example output:
```
Total                                                                 |          698.6719 KB
|-- debug/demo/memory.php line 17 > Xicrow\PhpDebug\Profiler::start() |            1.1016 KB
|-- Loop level 1 #1                                                   |            8.6484 KB
|-- |-- Loop level 2 #1                                               |            3.4844 KB
|-- |-- |-- Loop level 3 #1                                           |          896.0000 B
|-- |-- |-- Loop level 3 #2                                           |          896.0000 B
|-- |-- Loop level 2 #2                                               |            3.4375 KB
|-- |-- |-- Loop level 3 #3                                           |          896.0000 B
|-- |-- |-- Loop level 3 #4                                           |          928.0000 B
|-- Loop level 1 #2                                                   |            8.4609 KB
|-- |-- Loop level 2 #3                                               |            3.4062 KB
|-- |-- |-- Loop level 3 #5                                           |          896.0000 B
|-- |-- |-- Loop level 3 #6                                           |          896.0000 B
|-- |-- Loop level 2 #4                                               |            3.4062 KB
|-- |-- |-- Loop level 3 #7                                           |          896.0000 B
|-- |-- |-- Loop level 3 #8                                           |          896.0000 B
|-- callback: time                                                    |         1008.0000 B
|-- callback: strpos                                                  |            1.0234 KB
|-- callback: array_sum                                               |            1.5000 KB
|-- callback: array_rand                                              |            1.5078 KB
|-- callback: min                                                     |            1.4922 KB
|-- callback: max                                                     |            1.4922 KB
|-- callback: Xicrow\PhpDebug\Debugger::getDebugInformation           |            1.5234 KB
|-- callback: closure                                                 |          896.0000 B
|-- 5 B                                                               |            5.0000 B
|-- 5 KB                                                              |            5.0000 KB
|-- 5 MB                                                              |            5.0000 MB
|-- 5 GB                                                              |            5.0000 GB
|-- 5 TB                                                              |            5.0000 TB
|-- Random data generation: 0.62 MB                                   |          635.8594 KB
```
