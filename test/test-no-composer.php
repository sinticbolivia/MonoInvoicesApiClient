<?php
define('SB_DS', DIRECTORY_SEPARATOR);
//##incluir esta linea para cargar toda la libreria
require_once dirname(__DIR__) . SB_DS . 'src' . SB_DS . 'autoload.php';
//##lineas para cargar las clases necesarias
use SinticBolivia\MonoInvoicesApi\Classes\Factura;
use SinticBolivia\MonoInvoicesApi\Classes\FacturaItem;
use SinticBolivia\MonoInvoicesApi\Classes\TiposDocumentoSiat;

//##creacion de la factura
$factura = new Factura();
$factura->codigo_documento_sector 	= 1; //compra venta
$factura->codigo_metodo_pago 		= 1; //efectivo
$factura->codigo_moneda				= 1; //boliviano
$factura->codigo_sucursal			= 0;
$factura->complemento				= null;
$factura->customer					= 'Perez';
$factura->customer_email			= 'marce_nick@hotmail.com';
$factura->discount					= 0;
$factura->monto_giftcard			= 0;
$factura->nit_ruc_nif				= '3301267';
$factura->punto_venta				= 0;
$factura->tipo_cambio				= 1;
$factura->tipo_documento_identidad	= TiposDocumentoSiat::CI;
$factura->tipo_factura_documento	= 1;
//##crear el detalle de la factura
$item = new FacturaItem();
$item->codigo_actividad 	= '620900';
$item->codigo_producto_sin	= '83141';
$item->discount				= 0;
$item->price				= 123;
$item->product_code			= 'P0008';
$item->product_name			= 'Asesoria Desarrollo PHP';
$item->quantity				= 1;
$item->unidad_medida		= 58;
$item->total				= $item->quantity * $item->price;

//##adicionar el item/detalle a la factura
$factura->items[] = $item;

try
{
	$api = mi_instance_api();
	mi_login('1bytev2', '1bytev2');
	$res = mi_enviar_factura($factura);
	print_r($res);
}
catch(\SinticBolivia\MonoInvoicesApi\Classes\ExceptionApi $e)
{
	echo "ERROR API: ", $e->getMessage(), "\n";
	print_r($e->response->body);
}
catch(Exception $e)
{
	print "ERROR GENERAL: " . $e->getMessage();
}
