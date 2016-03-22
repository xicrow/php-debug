<?php
require_once('autoload.php');

use \Xicrow\Debug\Debugger;
use \Xicrow\Debug\Memory;
use \Xicrow\Debug\Timer;

// Set debugger options
Debugger::$documentRoot   = 'E:\\GitHub\\';
Debugger::$showCalledFrom = false;

/**
 * Debugger utility functions
 */
function debug($data) {
	Debugger::debug($data);
}

/**
 * Memory utility functions
 */
function memoryStart($key = null) {
	return Memory::start($key);
}

function memoryStop($key = null) {
	return Memory::stop($key);
}

function memoryCustom($key = null, $start = null, $stop = null) {
	return Memory::custom($key, $start, $stop);
}

function memoryCallback($key = null, $callback) {
	return Memory::callback($key, $callback);
}

function memoryShow($key = null, $options = []) {
	Memory::show($key, $options);
}

function memoryShowAll($options = []) {
	Memory::showAll($options);
}

/**
 * Timer utility functions
 */
function timerStart($key = null) {
	return Timer::start($key);
}

function timerStop($key = null) {
	return Timer::stop($key);
}

function timerCustom($key = null, $start = null, $stop = null) {
	return Timer::custom($key, $start, $stop);
}

function timerCallback($key = null, $callback) {
	return Timer::callback($key, $callback);
}

function timerShow($key = null, $options = []) {
	Timer::show($key, $options);
}

function timerShowAll($options = []) {
	Timer::showAll($options);
}
