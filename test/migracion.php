<?php
define('SB_FRAMEWORK_PATH', dirname(dirname(__DIR__)) . '/SBFramework');
require_once dirname(__DIR__) . '/vendor/autoload.php';
//##include SBFramework init
require_once SB_FRAMEWORK_PATH . '/init.php';
use SinticBolivia\MonoInvoicesApi\Classes\Migrator;
use SinticBolivia\MonoInvoicesApi\Classes\ExceptionApi;



$servidor = 'https://facturacion.1bytebo.net';
$usuario = '1bytev2';
$pwd = '1bytev2';
$for_user_id = 16;
try 
{
	$migrator = new Migrator($servidor, $usuario, $pwd);
	$migrator->init();
	$migrator->migrate($for_user_id);
} 
catch(ExceptionApi $e)
{
	print "API ERROR\n";
	print $e->response->body . "\n";
}
catch (Exception $e) 
{
	print "ERROR GENERAL: " . $e->getMessage();
}
