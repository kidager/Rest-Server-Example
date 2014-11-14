<?php
/**
 * @Last Modified time: 2014-11-14 22:04:58
 */

require 'RestServer.php';
require 'DvdController.php';

$restServer = new RestServer();
$restServer->addClass('DvdController');
$restServer->handle();