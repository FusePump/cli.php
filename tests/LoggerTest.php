<?php
/**
 * Log test
 */
require_once "lib/FusePump/Cli/Logger.php";

use FusePump\Cli\Logger as Logger;

class LogTest extends PHPUnit_Framework_TestCase {
    /**
     * Test log
     */
    function testLog() {
        // Simple output
        Logger::log('Hello');
        $this->expectOutputString("[".date('Y-m-d H:i:s')."] [LOG] [LoggerTest.php] [" . (__LINE__ - 1) . "] Hello\n");
    }

    /**
     * Test colour log
     */
    function testColourLog() {
        Logger::log('Hello', array('colour' => 'red'));
        $line = __LINE__ - 1;
        // check for red output
        $string = "\033[0;31m";
        $string .= "[".date('Y-m-d H:i:s')."] [LOG] [LoggerTest.php] [$line] Hello";
        $string .= "\033[0m";
        $string .= "\n";
        $this->expectOutputString($string);
    }

    /**
     * Test custom format
     */
    function testCustomFormat() {
        Logger::log('Hello', array(
            'format' => "[%s] [%s] %s",
            'inputs' => array(
                'LOG',
                'LoggerTest.php'
            )
        ));
        $this->expectOutputString("[LOG] [LoggerTest.php] Hello\n");
    }

    /**
     * Test out
     */
    function testOut() {
        Logger::out('Hi');
        $this->expectOutputString("Hi\n");
    }

    /**
     * Test out colour
     */
    function testOutColour() {
        Logger::out('Hi', 'red');
        $string = "\033[0;31m";
        $string .= "Hi";
        $string .= "\033[0m";
        $string .= "\n";
        $this->expectOutputString($string);
    }
}
