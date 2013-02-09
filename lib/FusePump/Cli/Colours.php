<?php
/**
 * CLI colour class.
 * @author Jonathan Kim <jonathan.kim@fusepump.com>
 * @author Leigh Simpson <leigh.simpson@fusepump.com>
 * @copyright Copyright (c) 2012 FusePump Ltd.
 * @license Licensed under the MIT license, see LICENSE.md for details
 */

namespace FusePump\Cli;

/**
 * CLI colours
 */
class Colours
{
    /**
     * Mapping of foreground colours to control codes.
     * @var array[string]
     */
    private static $foreground_colours = array(
        'black' => '0;30',
        'dark_gray' => '1;30',
        'red' => '0;31',
        'bold_red' => '1;31',
        'green' => '0;32',
        'bold_green' => '1;32',
        'brown' => '0;33',
        'yellow' => '1;33',
        'blue' => '0;34',
        'bold_blue' => '1;34',
        'purple' => '0;35',
        'bold_purple' => '1;35',
        'cyan' => '0;36',
        'bold_cyan' => '1;36',
        'white' => '1;37',
        'bold_gray' => '0;37'
    );


    /**
     * Mapping of background colours to control codes.
     * @var array[string]
     */
    private static $background_colours = array(
        'black' => '40',
        'red' => '41',
        'magenta' => '45',
        'yellow' => '43',
        'green' => '42',
        'blue' => '44',
        'cyan' => '46',
        'light_gray' => '47'
    );


    /**
     * Adds colouring control codes to a string.
     * @param string $string String to format.
     * @param string $foreground_colour Optional foreground colour for string.
     * @param string $background_colour Optional background colour for string.
     * @return string Formatted string.
     */
    public static function string($string, $foreground_colour = null, $background_colour = null)
    {
        $coloured_string = "";

        // Check if given foreground colour found
        if (isset(self::$foreground_colours[$foreground_colour])) {
            $coloured_string .= "\033[" . self::$foreground_colours[$foreground_colour] . "m";
        }
        // Check if given background colour found
        if (isset(self::$background_colours[$background_colour])) {
            $coloured_string .= "\033[" . self::$background_colours[$background_colour] . "m";
        }

        // Add string and end colouring
        $coloured_string .=  $string . "\033[0m";

        return $coloured_string;
    }


    /**
     * Returns the list of foreground colour names.
     * @return array[string]
     */
    public static function getForegroundColours()
    {
        return array_keys(self::$foreground_colours);
    }


    /**
     * Returns the list of background colour names.
     * @return array[string]
     */
    public static function getBackgroundColours()
    {
        return array_keys(self::$background_colours);
    }
}
