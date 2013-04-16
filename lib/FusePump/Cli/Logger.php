<?php

namespace FusePump\Cli;

require_once dirname(__FILE__) . '/Colours.php';

/**
 * Log class
 *
 * @author    Jonathan Kim <jonathan.kim@fusepump.com>
 * @copyright Copyright (c) 2013 FusePump Ltd.
 * @license   Licensed under the MIT license, see LICENSE.md for details
 */
class Logger
{
    public static $format = "[%s] [%s] [%s] [%d] %s";
    public static $inputs = array();
    public static $outputs = array(
        'STDOUT' => 'php://output',
        'STDERR' => 'php://stderr'
    );

    public static $errorColour = 'red';
    public static $eol = PHP_EOL;

    /**
     * Output log message
     *
     * @param string $message
     * @param array  $options
     * @static
     */
    public static function log($message, $options = array())
    {
        if (array_key_exists('inputs', $options)) {
            $inputs = $options['inputs'];
        } else {
            $inputs = array(
                self::getTimestamp(),
                'LOG',
                self::getFilename(),
                self::getLineNumber()
            );
        }

        if (!array_key_exists('output', $options)) {
            $options['output'] = 'STDOUT';
        }

        if (array_key_exists('format', $options)) {
            array_unshift($inputs, $options['format']);
        } else {
            array_unshift($inputs, self::$format);
        }

        $inputs[] = $message;

        $logMessage = self::getLogMessage($inputs);
        if (array_key_exists('colour', $options) && !empty($options['colour'])) {
            self::out($logMessage, $options['colour'], $options['output']);
        } else {
            self::out($logMessage, false, $options['output']);
        }
    }

    /**
     * Output error message
     *
     * @param string $message - message to output
     * @param array  $options - array of options
     * @static
     */
    public static function error($message, $options = array())
    {
        $options['colour'] = self::$errorColour;
        $options['inputs'] = array(
            self::getTimestamp(),
            'ERROR',
            self::getFilename(),
            self::getLineNumber()
        );
        if (!array_key_exists('output', $options)) {
            $options['output'] = 'STDERR';
        }
        self::log($message, $options);
    }

    /**
     * Output warn message
     *
     * @param string $message
     * @param array  $options
     * @static
     */
    public static function warn($message, $options = array())
    {
        $options['inputs'] = array(
            self::getTimestamp(),
            'WARN',
            self::getFilename(),
            self::getLineNumber()
        );
        self::log($message, $options);
    }

    /**
     * Output message
     *
     * @param string $message - message to output
     * @param mixed  $colour  - colour name
     * @param string $output  - where to send output, default is STDOUT
     * @static
     */
    public static function out($message, $colour = false, $output = 'STDOUT')
    {
        if (array_key_exists($output, self::$outputs)) {
            $output = self::$outputs[$output];
        }

        if ($colour) {
            file_put_contents($output, Colours::string($message, $colour).self::$eol, FILE_APPEND);
        } else {
            file_put_contents($output, $message.self::$eol, FILE_APPEND);
        }
    }

    /**
     * Get timestamp
     *
     * @static
     * @return string - nice formatted timestamp
     */
    public static function getTimestamp()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get log message
     *
     * @param array $inputs
     * @static
     *
     * @return mixed
     */
    public static function getLogMessage($inputs)
    {
        return call_user_func_array('sprintf', $inputs);
    }

    /**
     * Get filename of script calling logger
     *
     * @static
     * @return string
     */
    private static function getFilename()
    {
        $bt = debug_backtrace();
        $file = $bt[1];
        if (isset($file['file'])) {
            return basename($file['file']);
        } else {
            return '';
        }
    }

    /**
     * Get line number of where the log is called from
     *
     * @static
     * @return string
     */
    private static function getLineNumber()
    {
        $bt = debug_backtrace();
        $file = $bt[1];
        if (isset($file['line'])) {
            return basename($file['line']);
        } else {
            return '';
        }
    }
}
