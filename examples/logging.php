<?php
/**
 * Logging examples
 */

require dirname(__FILE__)."/../lib/FusePump/Cli/Logger.php";

use FusePump\Cli\Logger as Logger;

Logger::log('Hello!'); // => [2012-11-15 18:12:34] [LOG] [logging.php] Hello!
Logger::log('This is red!', array('colour' => 'red')); // => [2012-11-15 18:12:34] [LOG] [logging.php] This is red!
Logger::log('This is green!', array('colour' => 'green')); // => [2012-11-15 18:12:34] [LOG] [logging.php] This is green!

Logger::log('Custom formatting options!', array('format' => '[%s] %s', 'inputs' => array('custom_log'))); // => [Custom log] Custom formatting options!

Logger::warn('This is a warning'); // => [2012-11-15 18:12:34] [WARN] [logging.php] This is a warning

Logger::error('This is an error'); // => [2012-11-15 18:12:34] [ERROR] [logging.php] This is an error

Logger::out('Plain output'); // => Plain output
Logger::out('Plain output with colour!', 'red'); // => Plain output with colour!

// Log to a file
Logger::log('Log to a file', array('output' => 'output.log'));
Logger::error('Log an error to a file', array('output' => 'output.err'));
Logger::out('Log some text to a file', false, 'output.log');

