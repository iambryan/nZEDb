<?php
require_once dirname(__FILE__) . '/../../../www/config.php';

use nzedb\ColorCLI;
use nzedb\utility\Utility;

echo (new ColorCLI())->warning("This file is deprecated and will be removed in a future version.\nUse 'php cli/update_db.php 1' instead");
system(
	(Utility::hasCommand("php5") ? 'php5 ' : 'php ') .
	nZEDb_ROOT . 'cli' . DS . "update_db.php true" .
	(isset($argv[1]) && $argv[1] === "safe" ? ' safe' : '')
);
