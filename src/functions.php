<?php
use SinticBolivia\MonoInvoicesApi\Classes\MonoInvoicesApi;
use SinticBolivia\MonoInvoicesApi\Classes\Factura;

@session_start();

function mi_instance_api()
{
	static $api;
	
	if( $api )
		return $api;
	
	$api_url = defined('MI_API_URL') ? MI_API_URL : 'https://facturacion.1bytebo.net';
	$api = new MonoInvoicesApi($api_url);
	mi_restaurar_token();
	
	return $api;
}
/**
 * Inicio session en la API y obtiene un token
 * 
 * @param string $username
 * @param string $pass
 * @return object Objeto con datos de usuario y token
 */
function mi_login($username, $pass)
{
	if( mi_token_valido() )
	{
		return mi_resturar_sesion();
	}
		
	$api = mi_instance_api();
	$res = $api->login($username, $pass);
	mi_guardar_token($res->data->token);
	mi_guardar_usuario($res->data->user);
	return $res->data;
}
function mi_guardar_token($token)
{
	$_SESSION['MI_API_TOKEN'] = $token;
}
function mi_guardar_usuario($user)
{
	$_SESSION['MI_API_USER'] = $user;
}
function mi_restaurar_token()
{
	if( !isset($_SESSION['MI_API_TOKEN']) )
		return false;
	$api = mi_instance_api();
	$api->token = $_SESSION['MI_API_TOKEN'];
	return true;
}
function mi_token_valido()
{
	return isset($_SESSION['MI_API_TOKEN']);
}
function mi_resturar_sesion()
{
	return (object)[
		'user' => $_SESSION['MI_API_USER'],
		'token' => $_SESSION['MI_API_TOKEN']
	];
}
/**
 * Enviar la factura a la API
 *
 * @param Factura $factura
 * @return object
 */
function mi_enviar_factura(Factura $factura)
{
	$api = mi_instance_api();
	//mi_login($username, $pass)
	$res = $api->crearFactura($factura);
	
	return $res;
}
/**
 * 
 * @param int $id Identificador de la factura
 * @param int $codigoMotivo Codigo motivo anulacion
 */
function mi_anular_factura($id, $codigoMotivo)
{
	$api = mi_instance_api();
	$res = $api->anularFactura($id, $codigoMotivo);
	
	return $res->data;
}
/**
 * Obtiene el pdf de la factura
 * 
 * @param int $id Identificador de la factura
 * @param string $tpl Plantilla de la factura [rollo|oficio]
 * @return string Buffer de la factura en base64
 */
function mi_factura_pdf($id, $tpl = 'rollo')
{
	$api = mi_instance_api();
	$res = $api->obtenerPdf($id, $tpl);
	return $res->data->buffer;
}