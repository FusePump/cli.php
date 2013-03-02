# CLI php helper

A small collection of php classes to help with CLI utilities. 

## Installation 

Add this to your `composer.json`
    
    {
        "require": {
            "fusepump/cli.php": "0.4.2"
        }
    }

Then run:

    composer install

And add `require 'vendor/autoload.php'` to your php file;

## Tests 

To run tests:
    
    phpunit

## Contents

* [Inputs](#inputs)
* [Logger](#logger)
* [Utils](#utils)
    - [exec](#exec)
    - [jsonDecode](#jsonDecode)
    - [checkEnv](#checkenv)
    - [pregMatchArray](#pregmatcharray)
* [Timer](#timer)

- - -

## Inputs

Parse command line inputs

__Example:__
```php
use FusePump\Cli\Inputs as Inputs;

$cli = new Inputs($argv);

$cli->option('-h, --ham', 'Add ham');
$cli->option('-m, --mayo', 'Add mayonaise');
$cli->option('-c, --cheese [type]', 'Add a cheese');
$cli->option('-b, --bread [type]', 'Type of bread', true); // required input

$cli->param('when', 'When do you want the sandwhich (now, tomorrow or never)', true);

if(!$cli->parse()) {
    exit(1);    
}

echo "You ordered a sandwich ".$cli->get('when')." with: \n";
if($cli->get('-h')) echo " - Ham \n";
if($cli->get('-m')) echo " - Mayonaise \n";
if($cli->get('--cheese')) echo ' - '.$cli->get('--cheese')." cheese \n";
echo 'On '.$cli->get('-b')." bread \n";
```

__Run:__

```bash
php cli.php now -h -c cheddar --bread white
```

__Gives:__
    
```
You ordered a sandwich now with:
 - Ham
 - Cheddar cheese
On white bread
```

### Prompts

Prompt the user for information.

__Example:__
    
```php
use FusePump\Cli\Inputs as Inputs;

$cli = new Inputs();

$username = $cli->prompt('Username: ');
echo 'Got '.$username."\n";

$password = $cli->prompt('Password (not a real one): ', true);
echo 'Got '.$password."\n";

$confirm = $cli->confirm('Confirm? ');
echo var_dump($confirm);
```

__N.B.__ Confirm returns true on a "y" or "yes". It returns false otherwise. 

- - -

## Logger

Logger class with colour and timestamps.

__Example:__
    
```php
use FusePump\Cli\Logger as Logger;

Logger::log('Hello!'); 
// => [2012-11-15 18:12:34] [log] [logging.php] Hello!

Logger::log('This is red!', array('colour' => 'red')); 
// => [2012-11-15 18:12:34] [log] [logging.php] This is red!

Logger::log('This is green!', array('colour' => 'green')); 
// => [2012-11-15 18:12:34] [log] [logging.php] This is green!

Logger::log('Custom formatting options!', array('format' => '[%s] %s', 'inputs' => array('custom_log'))); 
// => [Custom log] Custom formatting options!

Logger::error('This is an error'); 
// => [2012-11-15 18:12:34] [error] [logging.php] This is an error

Logger::out('Plain output'); 
// => Plain output

Logger::out('Plain output with colour!', 'red'); 
// => Plain output with colour!
```

- - -

## Utils

Collection of utility functions.

### exec

Execute a shell command. Throws an exception if command fails.

__Example:__

```php
use FusePump\Cli\Utils as Utils;

Utils::exec('echo hello!'); // => hello!

$value = Utils::exec('echo hello!', true);
echo $value; // => hello!
```

### jsonDecode

Decode JSON string into an associative array. Throw exception with information if fails to parse JSON string.

__Example:__

```php
$json = FusePump\Cli\Utils::jsonDecode('{"hello": "world"}');

echo $json['hello']; // => world
```
    
### checkEnv

Check that an environmental variable is set. Can take a single variable or an
array of variables to set. Throws an Exception if the variable is not set. 

__Example:__

```php
use FusePump\Cli\Utils as Utils;

Utils::checkEnv(array(
    'FOO',
    'BAR'
));
    
Utils::checkEnv('FOO');
```

### pregMatchArray

Match subject to an array of regex patterns. Returns true if found a match.
False if not.

__Example:__

```php
use FusePump\Cli\Utils as Utils;

Utils::pregMatchArray(array(
    '/foo/i',
    '/bar/i'
), 'foo'); // => true
```
    
- - -

## Timer

Time blocks of code.

__Example:__

```php
use FusePump\Cli\Timer as Timer;

$clock = new Timer();

$clock->start('total');

$clock->start('block1');
sleep(2);
$clock->stop('block1');

// Averages
for($i = 0; $i < 5; $i++) {
    $rand = rand(0, 10);
    $clock->startAvg('loop');
    sleep($rand);
    $clock->stopAvg('loop');
}

$clock->stop('total');

$block1 = $clock->get('block1');
// =>
//  Array
//  (
//      [start] => 1355855924.4966
//      [start-line] => 12
//      [stop] => 1355855926.4968
//      [stop-line] => 14
//      [total] => 2.0002529621124
// )


$avg = $clock->getAvg('loop');
// =>
//  Array
//  (
//      [count] => 5
//      [total] => 2.3014297485352
//      [start] => 1355855931.3992
//      [start-line] => 23
//      [stop] => 1355855931.7995
//      [stop-line] => 25
//      [avg] => 0.46028594970703
//  )


$clock->report();
// =>  
//  Timing report:
//      total (10-28): 7.0035 seconds
//
//  Averages:
//      loop [5] (23-25): 4.0034 seconds


$clock->report('total');
// =>    
//  Timing report:
//      total (10-28): 7.0035 seconds
```

Report format:

    {{block name}} ({{lines block wraps}}): {{time taken}} seconds
    
Average report format:

    {{block name}} [{{number of iterations]}] ({{lines block wraps}}): {{time taken}} seconds
    
You can also enable or disable the clock to save resources:

```php
$clock = new Timer(false); // disable in the constructor

$clock->disable(); // disable after class has been instantiated

$clock->enable(); // enable class again

$clock->getEnabled(); // => true
```

## License

MIT
