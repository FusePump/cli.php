<?php

namespace FusePump\Cli;

/**
 * Utility class for useful static functions
 *
 * @author    Jonathan Kim <jonathan.kim@fusepump.com>
 * @copyright Copyright (c) 2013 FusePump Ltd.
 * @license   Licensed under the MIT license, see LICENSE.md for details
 */
class Utils
{
    /**
     * Execute a shell command
     *
     * @param string $cmd            - command to execute
     * @param bool   $return_content - capture output of command and return it
     *
     * @static
     *
     * @return array|bool|string - output of cmd if $return_content is set. Otherwise true
     * @throws \Exception - if shell command fails
     */
    public static function exec($cmd, $return_content = false)
    {
        $return_var = 0;
        $output = true;

        // if return content then start output buffer to capture output
        if ($return_content) {
            $cmd = $cmd . ' 2>&1';
        }

        exec($cmd, $output, $return_var);

        if ($return_content) {
            $output = trim(implode("\n", $output));
        } else {
            if (!empty($output)) {
                echo implode("\n", $output) . "\n";
            }
        }

        // if command exits with a code other than 0 throw exception
        if ($return_var > 0) {
            throw new \Exception($cmd . ' failed with exit code ' . $return_var . "\nMessage:\n" . $output);
        }

        return $output;
    }

    /**
     * Decode JSON string and throw error if fails
     *
     * @param string $string - JSON string to decode
     *
     * @static
     *
     * @return mixed - associative array
     * @throws \Exception if json decode fails with message about why
     */
    public static function jsonDecode($string)
    {
        $json = json_decode($string, true);

        // if json_decode failed
        if ($json == null) {
            switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new \Exception('Maximum stack depth exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new \Exception('Underflow or the modes mismatch');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new \Exception('Unexpected control character found');
                break;
            case JSON_ERROR_SYNTAX:
                throw new \Exception('Syntax error, malformed JSON');
                break;
            case JSON_ERROR_UTF8:
                throw new \Exception('Malformed UTF-8 characters, possibly incorrectly encoded');
                break;
            default:
                throw new \Exception('Unknown error');
                break;
            }
        }

        return $json;
    }

    /**
     * Check environment variables (array or single variable)
     *
     * @param mixed $variables - array or single variable to check
     *
     * @static
     *
     * @return bool - true is variables are set
     * @throws \Exception - if variable not set
     */
    public static function checkEnv($variables)
    {
        if (!is_array($variables)) {
            if (!getenv($variables)) {
                throw new \Exception('Variable ' . $variables . ' is not set');
            }
        } else {
            foreach ($variables as $variable) {
                if (!getenv($variable)) {
                    throw new \Exception('Variable ' . $variable . ' is not set');
                }
            }
        }

        return true;
    }

    /**
     * Preg match array
     *
     * Match subject to an array of regex patterns
     *
     * @param array  $patterns - array of regex patterns
     * @param string $subject  - string to test patterns on
     *
     * @static
     *
     * @return bool - true if found, false otherwise
     * @throws \Exception - if pattern is not an array or subject not a string
     */
    public static function pregMatchArray($patterns, $subject)
    {
        if (!is_array($patterns)) {
            throw new \Exception('$patterns is not an array');
        }

        if (!is_string($subject)) {
            throw new \Exception('$subject is not a string');
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $subject)) {
                return true;
            }
        }

        return false;
    }
}
