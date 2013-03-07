<?php
/**
 * Hex Dump string
 */

require dirname(__FILE__).'/../lib/FusePump/Cli/Utils.php';

use FusePump\Cli\Utils as Utils;

echo Utils::hexDump('This is a string of data');
