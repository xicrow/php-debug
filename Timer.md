# Timer

## Example

See example in [demo/timer.php](demo/timer.php).

HTML output from demo:

```html
    <!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Timer</title>
	</head>

	<body>
		<style type="text/css">.xicrow-php-debug-box {
			margin: 5px 0;
			font-family: Menlo, Monaco, Consolas, monospace;
			font-weight: normal;
			font-size: 12px;
			display: grid;
			grid-template-rows:min-content 1fr min-content
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-header {
			margin: 0;
			padding: 10px;
			font: inherit;
			background-color: #333;
			border: none;
			border-radius: 0;
			color: #aaa;
			display: block;
			z-index: 1000;
			overflow: auto
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content {
			margin: 0;
			padding: 10px;
			font: inherit;
			background-color: #222;
			border: none;
			border-radius: 0;
			color: #ccc;
			z-index: 1000
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content pre {
			margin: 0;
			padding: 0;
			font: inherit;
			display: block;
			overflow: auto;
			tab-size: 4;
			-moz-tab-size: 4
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content pre .xicrow-php-debug-data-type-null {
			color: #b729d9
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content pre .xicrow-php-debug-data-type-boolean {
			color: #ff8400
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content pre .xicrow-php-debug-data-type-integer {
			color: #00bfff
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content pre .xicrow-php-debug-data-type-double {
			color: #90ee90
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content pre .xicrow-php-debug-data-type-string {
			color: #d3d3d3
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-content .xicrow-php-debug-timer:hover {
			background-color: #333
		}
		.xicrow-php-debug-box .xicrow-php-debug-box-footer {
			margin: 0;
			padding: 10px;
			font: inherit;
			background-color: #333;
			border: none;
			border-radius: 0;
			color: #666;
			display: block;
			z-index: 1000;
			overflow: auto
		}
		.xicrow-php-debug-compare {
			display: grid;
			grid-auto-flow: column;
			grid-column-gap: 10px;
			font-family: monospace
		}
		/*# sourceMappingURL=default.css.map */
		</style>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Error</div>
			<div class="xicrow-php-debug-box-content">
				<pre>Invalid callback sent to Timer::callback(): nonExistingFunction</pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/timer.php line 56</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">50 seconds</div>
			<div class="xicrow-php-debug-box-content">
				<pre><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;"><span style="color: #FF8400;"><span style="color: #B729D9;">|-- 50 seconds                                                                                       |           50.0000 S </span></span></span></span></div></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/timer.php line 78</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">All timers</div>
			<div class="xicrow-php-debug-box-content">
				<pre><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">Total                                                                                                |            0.2410 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- demo/timer.php line 38                                                                           |            0.0122 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- Loop level 1 #1                                                                                  |            0.0129 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- Loop level 2 #1                                                                              |            0.0060 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #1                                                                          |            0.0012 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #2                                                                          |            0.0019 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- Loop level 2 #2                                                                              |            0.0050 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #3                                                                          |            0.0019 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #4                                                                          |            0.0010 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- Loop level 1 #2                                                                                  |            0.0079 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- Loop level 2 #3                                                                              |            0.0038 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #5                                                                          |            0.0010 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #6                                                                          |            0.0000 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- Loop level 2 #4                                                                              |            0.0031 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #7                                                                          |            0.0010 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- |-- |-- Loop level 3 #8                                                                          |            0.0010 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: time                                                                                   |            0.0031 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: strpos                                                                                 |            0.0010 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: array_sum                                                                              |            0.0021 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: array_rand                                                                             |            0.0041 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: min                                                                                    |            0.0010 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: max                                                                                    |            0.0012 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: Xicrow\PhpDebug\Debugger::getDebugInformation                                          |            0.0222 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;">|-- callback: closure                                                                                |            0.0010 MS</span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;">|-- 0.5 seconds                                                                                      |          500.0000 MS</span></span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;"><span style="color: #FF8400;">|-- 5 seconds                                                                                        |            5.0000 S </span></span></span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;"><span style="color: #FF8400;"><span style="color: #B729D9;">|-- 50 seconds                                                                                       |           50.0000 S </span></span></span></span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;"><span style="color: #FF8400;"><span style="color: #B729D9;">|-- 5 minutes                                                                                        |            5.0000 M </span></span></span></span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;"><span style="color: #FF8400;"><span style="color: #B729D9;">|-- 5 hours                                                                                          |            5.0000 H </span></span></span></span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;"><span style="color: #FF8400;"><span style="color: #B729D9;">|-- 5 days                                                                                           |            5.0000 D </span></span></span></span></div><div class="xicrow-php-debug-timer"><span style="color: #56DB3A;"><span style="color: #1299DA;"><span style="color: #FF8400;"><span style="color: #B729D9;">|-- 5 weeks                                                                                          |            5.0000 W </span></span></span></span></div></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/timer.php line 81</div>
		</div>
	</body>
</html>
```

CLI output from demo:

```text
############################################## Error ###############################################
Invalid callback sent to Timer::callback(): nonExistingFunction
-------------------------------------- demo/timer.php line 56 --------------------------------------

############################################ 50 seconds ############################################
|-- 50 seconds                                                                                       |           50.0000 S
-------------------------------------- demo/timer.php line 78 --------------------------------------

############################################ All timers ############################################
Total                                                                                                |            1.0240 MS
|-- demo/timer.php line 38                                                                           |            0.0341 MS
|-- Loop level 1 #1                                                                                  |            0.0160 MS
|-- |-- Loop level 2 #1                                                                              |            0.0069 MS
|-- |-- |-- Loop level 3 #1                                                                          |            0.0010 MS
|-- |-- |-- Loop level 3 #2                                                                          |            0.0041 MS
|-- |-- Loop level 2 #2                                                                              |            0.0060 MS
|-- |-- |-- Loop level 3 #3                                                                          |            0.0031 MS
|-- |-- |-- Loop level 3 #4                                                                          |            0.0010 MS
|-- Loop level 1 #2                                                                                  |            0.0081 MS
|-- |-- Loop level 2 #3                                                                              |            0.0029 MS
|-- |-- |-- Loop level 3 #5                                                                          |            0.0010 MS
|-- |-- |-- Loop level 3 #6                                                                          |            0.0010 MS
|-- |-- Loop level 2 #4                                                                              |            0.0031 MS
|-- |-- |-- Loop level 3 #7                                                                          |            0.0012 MS
|-- |-- |-- Loop level 3 #8                                                                          |            0.0000 MS
|-- callback: time                                                                                   |            0.0072 MS
|-- callback: strpos                                                                                 |            0.0021 MS
|-- callback: array_sum                                                                              |            0.0050 MS
|-- callback: array_rand                                                                             |            0.0069 MS
|-- callback: min                                                                                    |            0.0029 MS
|-- callback: max                                                                                    |            0.0021 MS
|-- callback: Xicrow\PhpDebug\Debugger::getDebugInformation                                          |            0.0319 MS
|-- callback: closure                                                                                |            0.0010 MS
|-- 0.5 seconds                                                                                      |          500.0000 MS
|-- 5 seconds                                                                                        |            5.0000 S
|-- 50 seconds                                                                                       |           50.0000 S
|-- 5 minutes                                                                                        |            5.0000 M
|-- 5 hours                                                                                          |            5.0000 H
|-- 5 days                                                                                           |            5.0000 D
|-- 5 weeks                                                                                          |            5.0000 W
-------------------------------------- demo/timer.php line 81 --------------------------------------
```
