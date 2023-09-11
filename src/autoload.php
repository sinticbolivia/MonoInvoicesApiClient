<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'widgets.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

spl_autoload_register(function($className)
{
	$baseNamespace 	= 'SinticBolivia\\MonoInvoicesApi';
	$classPath 		= str_replace([$baseNamespace, '\\'],  ['', SB_DS], $className);
	$classFilename 	= __DIR__ . $classPath . '.php';
	//var_dump($className, $classPath, $classFilename);echo "\n\n";
	if( is_file($classFilename) )
		require_once $classFilename;
}, true, true);