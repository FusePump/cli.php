<?php

namespace FusePump\Cli;

require_once 'lib/FusePump/Cli/Colours.php';

class ColoursTest extends \PHPUnit_Framework_TestCase
{
    public function testSampleForegroundCode()
    {
        $this->assertEquals("\033[0;31m", Colours::getForegroundCode('red'));
    }
    
    public function testErrorForUnknownForegroundCode()
    {
        $this->setExpectedException('\Exception', 'Invalid foreground colour.');
        Colours::getForegroundCode('notacolour');
    }
    
    public function testSampleBackgroundCode()
    {
        $this->assertEquals("\033[41m", Colours::getBackgroundCode('red'));
    }
    
    public function testErrorForUnknownBackgroundCode()
    {
        $this->setExpectedException('\Exception', 'Invalid background colour.');
        Colours::getBackgroundCode('notacolour');
    }
    
    public function testResetCode()
    {
        $this->assertEquals("\033[0m", Colours::getResetCode());
    }
}
