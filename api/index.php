<?php

/**
 * This is the entry point for the APIs
 *
 * @author Francis Genet
 * @package Trunking.io
 * @version 1.0
 */

define("ROOT_PATH", dirname(__FILE__) . '/');

require_once 'helper/settings.php';
require_once 'helper/utils.php';

// CORS
header('Access-Control-Allow-Headers:Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control, X-Auth-Token');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Max-Age:86400');

require_once 'lib/restler/restler.php';

use Luracast\Restler\Restler;

$r = new Restler();
$r->setSupportedFormats('JsonFormat');
$r->addAPIClass('tempaccount');
$r->addAPIClass('accounts');
//$r->addAuthenticationClass('AccessControl');
$r->handle();

?>
