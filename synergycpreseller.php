<?php

if (!defined('WHMCS')) {
	die('This file cannot be accessed directly.');
}

ini_set('display_errors', 'Off');
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);

require __DIR__ . '/bootstrap/autoload.php';

use Scp\WhmcsReseller\App;
use Scp\WhmcsReseller\Whmcs;

/**
 * Define WHMCS global functions
 *
 * @param string $class
 */
function _synergycpreseller_map_class($class) {
	foreach ($class::functions() as $name => $method) {
		$fullName = 'synergycpreseller_' . $name;
		eval('function ' . $fullName . ' (array $params)
        {
            return ' . App::class . '::get($params)
                ->make("' . $class . '")
                ->' . $method . '();
        }');
	}
}

function _synergycpreseller_map_static_class($class) {
	foreach ($class::staticFunctions() as $name => $method) {
		$fullName = 'synergycpreseller_' . $name;
		eval('function ' . $fullName . ' ()
       {
           return ' . $class . '::' . $method . '();
       }');
	}
}

_synergycpreseller_map_class(Whmcs\WhmcsConfig::class);
_synergycpreseller_map_class(Whmcs\WhmcsEvents::class);
_synergycpreseller_map_class(Whmcs\WhmcsButtons::class);
_synergycpreseller_map_class(Whmcs\WhmcsTemplates::class);
_synergycpreseller_map_static_class(Whmcs\Whmcs::class);
_synergycpreseller_map_static_class(Whmcs\WhmcsButtons::class);
