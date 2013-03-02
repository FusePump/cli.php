<?php

namespace FusePump\Cli;

/**
 * Timer class
 *
 * Usage:
 *  $clock = new Timer();
 *  $clock->start('block1');
 *  sleep(10);
 *  $clock->stop('block1');
 *
 *  $clock->start('block2');
 *  sleep(15);
 *  $clock->stop('block2');
 *
 *  $clock->report(); 
 *  // block1: 10 seconds
 *  // block2: 15 seconds
 *
 * @author    Jonathan Kim <jonathan.kim@fusepump.com>
 * @copyright Copyright (c) 2013 FusePump Ltd.
 * @license   Licensed under the MIT license, see LICENSE.md for details
 */
class Timer
{
    /**
     * @var array - stores timing blocks
     */
    protected $blocks = array();
    /**
     * @var array - average array
     */
    protected $avgs = array();
    /**
     * @var bool - enabled flag
     */
    protected $enabled;

    /**
     * Constructor
     */
    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * Enables timer
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disables timer
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Gets timer state (enabled or disabled)
     *
     * @return bool - true if enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
    
    /**
     * Starts timer
     *
     * @param string $block - key to identify block
     */
    public function start($block)
    {
        if ($this->enabled) {
            if (!isset($this->blocks[$block])) {
                $this->blocks[$block] = array();
            }

            $this->blocks[$block]['start'] = microtime(true);
            $this->blocks[$block]['start-line'] = $this->getLineNumber();
        }
    }

    /**
     * Stops timer
     *
     * @param $block - key to identify block
     *
     * @throws \Exception if block is not defined
     */
    public function stop($block)
    {
        if ($this->enabled) {
            if (!isset($this->blocks[$block])) {
                throw new \Exception('Block '.$block.' has not been started!');
            }

            $this->blocks[$block]['stop'] = microtime(true);
            $this->blocks[$block]['stop-line'] = $this->getLineNumber();
        }
    }

    /**
     * Start average block timer
     *
     * @param string $block - key to identify block
     */
    public function startAvg($block)
    {
        if ($this->enabled) {
            if (!isset($this->avgs[$block])) {
                $this->avgs[$block] = array();
                $this->avgs[$block]['count'] = 0;
                $this->avgs[$block]['total'] = 0;
            }

            $this->avgs[$block]['start'] = microtime(true);
            if (!isset($this->avgs[$block]['start-line'])) {
                $this->avgs[$block]['start-line'] = $this->getLineNumber();
            }

            // Initialise max and min time
            if (!isset($this->avgs[$block]['max-time'])) {
                $this->avgs[$block]['max-time'] = 0;
            }
            if (!isset($this->avgs[$block]['min-time'])) {
                $this->avgs[$block]['min-time'] = 9999;
            }
        }
    }

    /**
     * Stop average block and calculate average
     *
     * @param string $block - average block identifier
     *
     * @throws \Exception if block not defined
     */
    public function stopAvg($block)
    {
        if ($this->enabled) {
            if (!isset($this->avgs[$block])) {
                throw new \Exception('Average block '.$block.' has not been started!');
            }

            $this->avgs[$block]['stop'] = microtime(true);
            if (!isset($this->avgs[$block]['stop-line'])) {
                $this->avgs[$block]['stop-line'] = $this->getLineNumber();
            }

            // Calculate average
            $this->avgs[$block]['count']++; // increment count

            $time = $this->avgs[$block]['stop'] - $this->avgs[$block]['start'];

            // Check max and min time
            if ($this->avgs[$block]['max-time'] < $time) {
                $this->avgs[$block]['max-time'] = $time;
            }
            if ($this->avgs[$block]['min-time'] > $time) {
                $this->avgs[$block]['min-time'] = $time;
            }

            $this->avgs[$block]['total'] = $this->avgs[$block]['total'] + $time;
        }
    }

    /**
     * Print function
     *
     * @param string $block - optionally specify which block to print
     *
     * @return string
     * @throws \Exception if block does not exist
     */
    public function report($block = null)
    {
        if ($this->enabled) {
            $output = "";
            $output .= 'Timing report:'.PHP_EOL;
            if ($block === null) {
                foreach ($this->blocks as $key => $block) {
                    $output .= $this->printBlock($key);
                }

                $output .= PHP_EOL;
                $output .= 'Averages:'.PHP_EOL;
                foreach ($this->avgs as $key => $block) {
                    $output .= $this->printAvgBlock($key);
                }
            } else {
                try {
                    $output .= $this->printBlock($block);
                } catch (\Exception $e) {
                    try {
                        $output .= $this->printAvgBlock($block);
                    } catch (\Exception $e) {
                        throw new \Exception('Block does not exist in either average or normal blocks');
                    }
                }
            }
            $output .= PHP_EOL;
            return $output;
        }
    }

    /**
     * Print block
     *
     * @param string $block - key to identify block
     *
     * @return string output
     * @throws \Exception if block is not defined
     */
    private function printBlock($block)
    {
        if (!array_key_exists($block, $this->blocks)) {
            throw new \Exception('Block '.$block.' not defined');
        }
        $this->finishBlock($block);

        $output = "";
        $output .= "    $block";
        $output .= " (".$this->blocks[$block]['start-line']."-".$this->blocks[$block]['stop-line'].")";
        $output .= ": ";
        $output .= round($this->blocks[$block]['total'], 4);
        $output .= ' seconds';
        $output .= PHP_EOL;

        return $output;
    }

    /**
     * Print average block
     *
     * @param string $block - key to identify block
     *
     * @return string output
     * @throws \Exception if block not defined
     */
    private function printAvgBlock($block)
    {
        if (!array_key_exists($block, $this->avgs)) {
            throw new \Exception('Average block '.$block.' not defined');
        }
        $this->finishAvgBlock($block);

        $output = "";
        $output .= "    $block";
        $output .= " [".$this->avgs[$block]['count']."]";
        $output .= " (".$this->avgs[$block]['start-line']."-".$this->avgs[$block]['stop-line'].")";
        $output .= ": ";
        $output .= round($this->avgs[$block]['avg'], 4);
        $output .= ' seconds';
        $output .= PHP_EOL;

        // Output max and min time
        $output .= "        max time: ".round($this->avgs[$block]['max-time'], 4).PHP_EOL;
        $output .= "        min time: ".round($this->avgs[$block]['min-time'], 4).PHP_EOL;

        return $output;
    }

    /**
     * Get line number where command was called from
     *
     * @return mixed
     */
    private function getLineNumber()
    {
        $bg = debug_backtrace();
        return $bg[1]['line'];
    }

    /**
     * Get block info
     *
     * @param string $block - key to identify block
     *
     * @return mixed - associative array
     * @throws \Exception if block not defined
     */
    public function get($block)
    {
        if ($this->enabled) {
            if (!array_key_exists($block, $this->blocks)) {
                throw new \Exception('Block '.$block.' not defined');
            }

            $this->finishBlock($block);

            return $this->blocks[$block];
        }
    }

    /**
     * Get average block info
     *
     * @param string $block - key to identify block
     *
     * @return mixed - associative array
     * @throws \Exception
     */
    public function getAvg($block)
    {
        if ($this->enabled) {
            if (!array_key_exists($block, $this->avgs)) {
                throw new \Exception('Average block '.$block.' not defined');
            }

            $this->finishAvgBlock($block);

            return $this->avgs[$block];
        }
    }

    /**
     * Finish block
     *
     * @param string $block - key to identify block
     *
     * @return mixed
     * @throws \Exception
     */
    private function finishBlock($block)
    {
        if (!array_key_exists($block, $this->blocks)) {
            throw new \Exception('Block '.$block.' not defined');
        }
        $this->blocks[$block]['total'] = $this->blocks[$block]['stop'] - $this->blocks[$block]['start'];

        return $this->blocks[$block];
    }

    /**
     * Finish average block
     *
     * @param string $block - key to identify block
     *
     * @return mixed
     * @throws \Exception
     */
    private function finishAvgBlock($block)
    {
        if (!array_key_exists($block, $this->avgs)) {
            throw new \Exception('Average block '.$block.' not defined');
        }
        $this->avgs[$block]['avg'] = $this->avgs[$block]['total'] / $this->avgs[$block]['count'];

        return $this->avgs[$block];
    }
}
