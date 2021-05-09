# Debugger

## Example

See example in [demo/debugger.php](demo/debugger.php).

HTML output from demo:

```html
    <!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Debugger</title>
	</head>

	<body>
		<div class="xicrow-php-debug-compare">
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
			.xicrow-php-debug-timer:hover {
				background-color: #333
			}
			/*# sourceMappingURL=default.css.map */
			</style>
			<div class="xicrow-php-debug-box">
				<div class="xicrow-php-debug-box-header">Simple PHP types #1</div>
				<div class="xicrow-php-debug-box-content">
					<pre><span class="xicrow-php-debug-data-type-null">NULL</span></pre>
				</div>
				<div class="xicrow-php-debug-box-footer">demo/debugger.php line 43</div>
			</div>
			<div class="xicrow-php-debug-box">
				<div class="xicrow-php-debug-box-header">Simple PHP types #2</div>
				<div class="xicrow-php-debug-box-content">
					<pre><span class="xicrow-php-debug-data-type-boolean">TRUE</span></pre>
				</div>
				<div class="xicrow-php-debug-box-footer">demo/debugger.php line 43</div>
			</div>
			<div class="xicrow-php-debug-box">
				<div class="xicrow-php-debug-box-header">Simple PHP types #3</div>
				<div class="xicrow-php-debug-box-content">
					<pre><span class="xicrow-php-debug-data-type-string">123</span></pre>
				</div>
				<div class="xicrow-php-debug-box-footer">demo/debugger.php line 43</div>
			</div>
			<div class="xicrow-php-debug-box">
				<div class="xicrow-php-debug-box-header">Simple PHP types #4</div>
				<div class="xicrow-php-debug-box-content">
					<pre><span class="xicrow-php-debug-data-type-integer">123</span></pre>
				</div>
				<div class="xicrow-php-debug-box-footer">demo/debugger.php line 43</div>
			</div>
			<div class="xicrow-php-debug-box">
				<div class="xicrow-php-debug-box-header">Simple PHP types #5</div>
				<div class="xicrow-php-debug-box-content">
					<pre><span class="xicrow-php-debug-data-type-double">123.45</span></pre>
				</div>
				<div class="xicrow-php-debug-box-footer">demo/debugger.php line 43</div>
			</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Simple PHP type</div>
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-null">NULL</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 44</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Simple PHP type</div>
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-boolean">TRUE</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 44</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Simple PHP type</div>
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-boolean">FALSE</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 44</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Simple PHP type</div>
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-integer">123</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 44</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Simple PHP type</div>
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-double">123.123</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 44</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Simple PHP type</div>
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-string">string</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 44</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-content"><pre>[
	<span class="xicrow-php-debug-data-type-integer">0</span> => <span class="xicrow-php-debug-data-type-integer">1</span>,
	<span class="xicrow-php-debug-data-type-integer">1</span> => <span class="xicrow-php-debug-data-type-integer">2</span>,
	<span class="xicrow-php-debug-data-type-integer">2</span> => <span class="xicrow-php-debug-data-type-integer">3</span>
]</pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 45</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-content"><pre>object(Closure) {
	
}</pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 46</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-content"><pre>object(stdClass) {
	
}</pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 47</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-resource">Resource id #14 (stream)</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 48</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Trace</div>
			<div class="xicrow-php-debug-box-content"><pre>1: demo/debugger.php line 50
</pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 50</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Trace</div>
			<div class="xicrow-php-debug-box-content"><pre>2: demo/debugger.php line 35
1: demo/debugger.php line 51
</pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 35</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-header">Trace</div>
			<div class="xicrow-php-debug-box-content"><pre>3: demo/debugger.php line 35
2: demo/debugger.php line 40
1: demo/debugger.php line 52
</pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 35</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-string">demo/debugger.php line 54</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 54</div>
		</div>
		<div class="xicrow-php-debug-box">
			<div class="xicrow-php-debug-box-content">
				<pre><span class="xicrow-php-debug-data-type-string">Unknown trace with index: 1</span></pre>
			</div>
			<div class="xicrow-php-debug-box-footer">demo/debugger.php line 55</div>
		</div>
	</body>
</html>
```

CLI output from demo:

```text
####################################### Simple PHP types #1 ########################################
NULL
------------------------------------ demo/debugger.php line 42 -------------------------------------

####################################### Simple PHP types #2 ########################################
TRUE
------------------------------------ demo/debugger.php line 42 -------------------------------------

####################################### Simple PHP types #3 ########################################
"123"
------------------------------------ demo/debugger.php line 42 -------------------------------------

####################################### Simple PHP types #4 ########################################
123
------------------------------------ demo/debugger.php line 42 -------------------------------------

####################################### Simple PHP types #5 ########################################
123.45
------------------------------------ demo/debugger.php line 42 -------------------------------------

######################################### Simple PHP type ##########################################
NULL
------------------------------------ demo/debugger.php line 43 -------------------------------------

######################################### Simple PHP type ##########################################
TRUE
------------------------------------ demo/debugger.php line 43 -------------------------------------

######################################### Simple PHP type ##########################################
FALSE
------------------------------------ demo/debugger.php line 43 -------------------------------------

######################################### Simple PHP type ##########################################
123
------------------------------------ demo/debugger.php line 43 -------------------------------------

######################################### Simple PHP type ##########################################
123.123
------------------------------------ demo/debugger.php line 43 -------------------------------------

######################################### Simple PHP type ##########################################
"string"
------------------------------------ demo/debugger.php line 43 -------------------------------------

############################################## DEBUG ###############################################
[
        0 => 1,
        1 => 2,
        2 => 3
]
------------------------------------ demo/debugger.php line 44 -------------------------------------

############################################## DEBUG ###############################################
object(Closure) {

}
------------------------------------ demo/debugger.php line 45 -------------------------------------

############################################## DEBUG ###############################################
object(stdClass) {

}
------------------------------------ demo/debugger.php line 46 -------------------------------------

############################################## DEBUG ###############################################
Resource id #15 (stream)
------------------------------------ demo/debugger.php line 47 -------------------------------------

############################################## Trace ###############################################
1: demo/debugger.php line 49

------------------------------------ demo/debugger.php line 49 -------------------------------------

############################################## Trace ###############################################
2: demo/debugger.php line 34
1: demo/debugger.php line 50

------------------------------------ demo/debugger.php line 34 -------------------------------------

############################################## Trace ###############################################
3: demo/debugger.php line 34
2: demo/debugger.php line 39
1: demo/debugger.php line 51

------------------------------------ demo/debugger.php line 34 -------------------------------------

############################################## DEBUG ###############################################
"demo/debugger.php line 53"
------------------------------------ demo/debugger.php line 53 -------------------------------------

############################################## DEBUG ###############################################
"Unknown trace with index: 1"
------------------------------------ demo/debugger.php line 54 -------------------------------------
```
