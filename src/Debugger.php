<?php
namespace Xicrow\PhpDebug;

/**
 * Class Debugger
 *
 * @package Xicrow\PhpDebug
 */
class Debugger
{
    /**
     * @var string|null
     */
    public static $documentRoot = null;

    /**
     * @var bool
     */
    public static $showCalledFrom = true;

    /**
     * @var bool
     */
    public static $output = true;

    /**
     * @var bool
     */
    private static $outputStyles = true;

    /**
     * @param string $data
     * @param array  $options
     *
     * @codeCoverageIgnore
     */
    public static function output($data, array $options = [])
    {
        $options = array_merge([
            'trace_offset' => 0,
        ], $options);

        if (!self::$output || !is_string($data)) {
            return;
        }

        if (php_sapi_name() == 'cli') {
            echo str_pad(' DEBUG ', 100, '-', STR_PAD_BOTH);
            echo "\n";
            if (self::$showCalledFrom) {
                echo self::getCalledFrom($options['trace_offset'] + 2);
                echo "\n";
            }
            echo $data;
            echo "\n";
        } else {
            if (self::$showCalledFrom) {
                echo '<pre class="sf-dump sf-dump-called-from">';
                echo self::getCalledFrom($options['trace_offset'] + 2);
                echo '</pre>';
            }
            echo $data;
            if (self::$showCalledFrom && self::$outputStyles) {
                echo '<style type="text/css">';
                echo 'pre.sf-dump.sf-dump-called-from { margin-bottom: 0; color: #AAA; }';
                echo 'pre.sf-dump { margin-top: 0; }';
                echo '</style>';

                self::$outputStyles = false;
            }
        }
    }

    /**
     * @param mixed $data
     * @param array $options
     *
     * @codeCoverageIgnore
     */
    public static function debug($data, array $options = [])
    {
        $options = array_merge([
            'trace_offset' => 0,
        ], $options);

        $cloner = new \Symfony\Component\VarDumper\Cloner\VarCloner();
        if (php_sapi_name() == 'cli') {
            $dumper = new \Symfony\Component\VarDumper\Dumper\CliDumper();
        } else {
            $dumper = new \Symfony\Component\VarDumper\Dumper\HtmlDumper();
        }

        self::output($dumper->dump($cloner->cloneVar($data), true), $options);
    }

    /**
     * @param array $options
     *
     * @codeCoverageIgnore
     */
    public static function showTrace(array $options = [])
    {
        $options = array_merge([
            'trace_offset' => 0,
            'reverse'      => false,
        ], $options);

        $backtrace = ($options['reverse'] ? array_reverse(debug_backtrace()) : debug_backtrace());

        $output     = '';
        $traceIndex = ($options['reverse'] ? 1 : count($backtrace));
        foreach ($backtrace as $trace) {
            $output .= $traceIndex . ': ';
            $output .= self::getCalledFromTrace($trace);
            $output .= "\n";

            $traceIndex += ($options['reverse'] ? 1 : -1);
        }

        self::output($output);
    }

    /**
     * @param int $index
     *
     * @return string
     */
    public static function getCalledFrom($index = 0)
    {
        $backtrace = debug_backtrace();

        if (!isset($backtrace[$index])) {
            return 'Unknown trace with index: ' . $index;
        }

        return self::getCalledFromTrace($backtrace[$index]);
    }

    /**
     * @param array $trace
     *
     * @return string
     */
    public static function getCalledFromTrace($trace)
    {
        $traceDescription = '';
        if (empty($traceDescription) && isset($trace['file'])) {
            $traceDescription .= $trace['file'] . ' line ' . $trace['line'];
            $traceDescription = str_replace('\\', '/', $traceDescription);
            $traceDescription = (!empty(self::$documentRoot) ? substr($traceDescription, strlen(self::$documentRoot)) : $traceDescription);
            $traceDescription = trim($traceDescription, '/');
        }
        if (empty($traceDescription) && isset($trace['function'])) {
            $traceDescription .= (isset($trace['class']) ? $trace['class'] : '');
            $traceDescription .= (isset($trace['type']) ? $trace['type'] : '');
            $traceDescription .= $trace['function'] . '()';
        }
        if (empty($traceDescription)) {
            $traceDescription = 'Unable to get called from trace';
        }

        return $traceDescription;
    }
}
