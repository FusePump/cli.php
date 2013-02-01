<?php

namespace FusePump\Cli;

/**
 * Inputs class
 *
 * Parses command line inputs
 *
 */
class Inputs
{
    protected $options = array();
    protected $params = array(); // array of parameters
    protected $inputs = array();
    protected $pinputs = array(); // processed inputs
    protected $required = array();
    protected $name; // name of script
    protected $helpOption = "-h, --help Output usage information";

    /**
     * Constructor
     *
     * @param params - array of input parameters
     */
    public function __construct($inputs = null)
    {
        // remove name from script inputs
        $this->name = $inputs[0];
        unset($inputs[0]);
        if (!empty($inputs)) {
            $this->inputs = array_values($inputs);
        }
    }

    /**
     * Add option
     *
     * $flags -
     * $help - help text
     * $required
     *
     * Example:
     *    $cli->option('-p, --peppers', 'Add peppers');
     *    $cli->option('-c, --cheese [type]', 'Add a cheese', true);
     *    $cli->get('-p'); // true/false
     *    $cli->get('-c'); // cheese type
     */
    public function option($flags, $help, $required = false)
    {
        $options = $this->parseOption($flags);

        $options['help'] = $flags.' '.$help;

        if ($required) {
            $options['required'] = true;
        }

        $this->setOption($options['short'], $options);
    }

    /**
     * Add param
     *
     * $param - name of param
     * $help - help text
     * $required - flag
     *
     * Example:
     *      $cli->param('client', 'Name of client', true);
     *      $cli->get('client');
     * 
     */
    public function param($param, $help, $required = false)
    {
        $options = array();

        $options['name'] = $param;
        if ($required) {
            $options['help'] = '<'.$param.'> '.$help;
            $options['required'] = true;
        } else {
            $options['help'] = '['.$param.'] '.$help;
            $options['required'] = false;
        }

        $this->setParam($options);
    }

    /**
     * Parse options
     */
    private function parseOption($string)
    {
        $output = array();
        $exploded = explode(',', $string);

        $output['short'] = $exploded[0]; // short flag

        $regex = '/\[(.*)\]/';
        $output['long'] = $exploded[1];
        if (preg_match($regex, $exploded[1])) { // check for input
            $output['long'] = preg_replace($regex, '', $exploded[1]); // replace input from string
            $output['input'] = true; // set input as true
        }
        $output['long'] = trim($output['long']);

        return $output;
    }

    /**
     * Parse
     * Process inputs
     */
    public function parse()
    {
        // check if a help flag is set
        try {
            $key = $this->checkInputs('-h', '--help');
            if ($key !== false) {
                throw new \Exception('Help flag is set');
            }
        } catch (\Exception $e) {
            $this->outputHelp();
            return false;
        }

        // loop through options and see if they are in the inputs
        foreach ($this->options as $option => $info) {
            // if option is in inputs
            $key = $this->checkInputs($info['short'], $info['long']);
            if ($key === false) {
                $this->pinputs[$info['short']] = false;
                $this->pinputs[$info['long']] = false;
            } else {
                // check if next input should be in input
                if (array_key_exists('input', $info) && $info['input'] == true) {
                    $this->pinputs[$info['short']] = $this->inputs[$key + 1];
                    $this->pinputs[$info['long']] = $this->inputs[$key + 1];
                    unset($this->inputs[$key]); // remove flag from inputs array
                    unset($this->inputs[$key + 1]);
                } else {
                    $this->pinputs[$info['short']] = true;
                    $this->pinputs[$info['long']] = true;
                    unset($this->inputs[$key]);
                }
            }
        }

        // Loop through each of the remaining inputs and assign them to their
        // params
        $count = 0; // count because cannot rely on key value of inputs
        foreach ($this->inputs as $key => $input) {
            // If parameter for input exists
            if (array_key_exists($count, $this->params)) {
                $this->pinputs[$this->params[$count]['name']] = $input;
                unset($this->inputs[$key]); // remove input from inputs
            }
            $count++;
        }
       
        // loop through remaining flags and insert them at their indexes
        if (!empty($this->inputs)) {
            $this->inputs = array_values($this->inputs);
        }
        $this->pinputs =  array_merge($this->inputs, $this->pinputs);

        // check required inputs
        try {
            $this->checkRequired();
        } catch (\Exception $e) {
            echo $e->getMessage().PHP_EOL.PHP_EOL;
            $this->outputHelp(true);
            return false;
        }

        return true;
    }

    /**
     * Check input for flag
     */
    private function checkInputs($short, $long)
    {
        $key = array_search($short, $this->inputs);
        if ($key === false) {
            $key = array_search($long, $this->inputs);
            if ($key === false) {
                return false;
            } else {
                return $key;
            }
        } else {
            return $key;
        }
    }

    /**
     * Check required options
     * If a required option is not provided then throw and exception
     */
    private function checkRequired()
    {
        // Loop through all params
        foreach ($this->params as $param) {
            if (array_key_exists('required', $param) && $param['required'] == true) {
                if (!array_key_exists($param['name'], $this->pinputs)) {
                    throw new \Exception('Parameter "'.$param['name'].'" is required');
                }
            }
        }
 
        // Loop through all options
        foreach ($this->options as $key => $option) {
            // if option is required
            if (array_key_exists('required', $option) && $option['required'] == true) {
                // check that it is defined in pinputs
                if ($this->pinputs[$option['short']] == false) {
                    throw new \Exception('Option "'.$option['help'].'" is required');
                }
            }
        }

    }

    /**
     * Set option
     */
    public function setOption($name, $options)
    {
        $this->options[$name] = $options;
    }

    /**
     * Set param
     */
    public function setParam($options)
    {
        $this->params[] = $options;
    }

    /**
     * Get options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get inputs
     */
    public function getInputs()
    {
        return $this->pinputs;
    }

    /**
     * Get Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get input
     */
    public function get($flag)
    {
        if (!array_key_exists($flag, $this->pinputs)) {
            throw new \Exception('Input '.$flag.' cannot be found');
        } else {
            return $this->pinputs[$flag];
        }
    }

    /**
     * Prompt
     * Add a new prompt
     *
     * $msg - message to display
     * $password - is a password input or not (don't display text)
     *
     * N.B. Linux only
     *
     * Returns input from prompt
     */
    public static function prompt($msg, $password = null)
    {
        // output message
        echo $msg;
        // if password then disable text output
        if ($password != null) {
            system('stty -echo');
        }

        $input = trim(fgets(STDIN));

        if ($password != null) {
            system('stty echo');
            echo PHP_EOL; // output end of line because the user's CR won't have been outputted
        }
        return $input;
    }

    /**
     * Add a confirmation
     *
     * Returns true if yes, false if anything else
     */
    public static function confirm($msg)
    {
        echo $msg;
        $input = trim(fgets(STDIN));

        if (strtolower($input) == 'y' || strtolower($input) == 'yes') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Output help text
     *
     * $short - Short output text
     *
     * Format:
     *
     * Usage: {{name}} <param> [options]
     *
     * Options:
     *      -p, --peppers Add peppers
     *      -c, --cheese [type] Add a cheese
     *      -m, --mayo Add mayonaise
     *      -h, --help Output usage information
     */
    public function outputHelp($short = false)
    {
        echo "Usage: ".$this->getName()." ";
        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                if ($param['required'] == true) {
                    echo '<'.$param['name'].'> ';
                } else {
                    echo '['.$param['name'].'] ';
                }
            }
        }
        echo "[options]";

        if (!$short) {
            echo PHP_EOL.PHP_EOL;

            if (!empty($this->params)) {
                echo "Parameters:".PHP_EOL;
                foreach ($this->params as $param) {
                    echo "\t".$param['help'].PHP_EOL;
                }
                echo PHP_EOL;
            }

            echo "Options:".PHP_EOL;
            foreach ($this->options as $option) {
                $output = "\t".$option['help'];
                if (array_key_exists('required', $option) && $option['required'] == true) {
                    $output .= " [required]";
                }
                $output .= PHP_EOL;
                echo $output;
            }
            echo "\t".$this->helpOption.PHP_EOL;
        } else {
            echo PHP_EOL;
        }
    }
}
