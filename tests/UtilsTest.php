<?php
/**
 * Utils test
 */
require_once "lib/FusePump/Cli/Utils.php";

use FusePump\Cli\Utils as Utils;

class UtilsTest extends PHPUnit_Framework_TestCase {
    /**
     * Test exec function
     */
    function testExec() {
        Utils::exec('echo hello!');
        $this->expectOutputString("hello!\n");
    }

    function testExecMultiLine() {
        Utils::exec('echo "hello
world!"');
        $this->expectOutputString("hello\nworld!\n");
    }

    /**
     * Test exec with return var function
     */
    function testExecReturn() {
        $output = Utils::exec('echo hello!', true);
        $this->assertEquals("hello!", $output);
    }

    /**
     * Test exec fail
     * @expectedException Exception
     * @expectedExceptionMessage exit 1 2>&1 failed with exit code 1
     */
    function testExecFail() {
        Utils::exec('exit 1', true);
    }

    /**
     * Test JSON decode
     */
    function testJSONDecode() {
        $json = '{"hello": "world"}';
        $output = Utils::jsonDecode($json);

        $this->assertArrayHasKey('hello', $output);
        $this->assertEquals('world', $output['hello']);
    }

    /**
     * Test JSON decode on malformed JSON
     * @expectedException Exception
     * @expectedExceptionMessage Syntax error, malformed JSON
     */
    function testJSONDecodeMalformed() {
        $json = '{hello: "world}';
        Utils::jsonDecode($json);
    }

    /**
     * Test checkEnv 
     */
    function testCheckEnv() {
        putenv("FOO=BAR");
        Utils::checkEnv(array(
            'FOO'
        ));

        Utils::checkEnv('FOO');

        // Shouldn't throw an exception
        $this->assertTrue(true);
    }

    /**
     * Test checkEnv when variable is not set
     * @expectedException Exception
     * @expectedExceptionMessage Variable BAR is not set
     */
    function testCheckEnvException() {
        Utils::checkEnv(array(
            'BAR'
        ));
    }

    /**
     * Test checkEnv when variable is not set
     * @expectedException Exception
     * @expectedExceptionMessage Variable BAR is not set
     */
    function testCheckEnvExceptionSingleVariable() {
        Utils::checkEnv('BAR');
    }

    /**
     * Test pregMatchArray
     */
    function testPregMatchArray() {
        $result = Utils::pregMatchArray(array(
            '/foo/i',
            '/bar/i'
        ), 'foo');

        $this->assertTrue($result);

        $result = Utils::pregMatchArray(array(
            '/foo/i',
            '/bar/i'
        ), 'nothing');

        $this->assertFalse($result);
    }

    /**
     * Test pregMatchArray when input is not an array
     * @expectedException Exception
     * @expectedExceptionMessage $patterns is not an array
     */
    function testPregMatchNotArray() {
        Utils::pregMatchArray('/foo/i', 'foo');
    }

    /**
     * Test pregMatchArray when input is not a string
     * @expectedException Exception
     * @expectedExceptionMessage $subject is not a string
     */
    function testPregMatchNotString() {
        Utils::pregMatchArray(array('/foo/i'), array('foo'));
    }
}
