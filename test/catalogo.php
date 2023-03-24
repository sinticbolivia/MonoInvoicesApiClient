<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
//die(dirname(__DIR__));
use SinticBolivia\MonoInvoicesApi\Classes\MonoInvoicesApi;
use function SinticBolivia\MonoInvoicesApi\siat_widget_actividades;

function instanceApi()
{
	static $api;
	
	if( $api )
		return $api;
	$api = new MonoInvoicesApi('https://facturacion.1bytebo.net');
	
	return $api;
}
function testLogin()
{
	$api = instanceApi();
	$res = $api->login('1byte', '1byte_00_$');
	print_r($res);
}
function testUnidadesMedida()
{
	$api = instanceApi();
	$res = $api->unidadesMedida();
	print_r($res);
}
function testWidgets()
{
	$api = instanceApi();
	siat_widget_actividades($api);
}
testLogin();
testUnidadesMedida();
testWidgets();