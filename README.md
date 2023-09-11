# MonoInvoicesApiClient - Facturacion Electronica SIAT

Libreria para la conexion a la API de facturacion SIAT

### Instalacion composer

```
composer require sinticbolivia/mono-invoices-api
```

### Instalacion sin composer

Descargar el zip

Incluir la libreria en su sistema

```
```

## Ejemplo de Generacion de Factura

```
use SinticBolivia\MonoInvoicesApi\Classes\MonoInvoicesApi;
use SinticBolivia\MonoInvoicesApi\Classes\Factura;
use SinticBolivia\MonoInvoicesApi\Classes\ExceptionApi;
use SinticBolivia\MonoInvoicesApi\Classes\FacturaItem;

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
	$res = $api->login('1byte', '1byte');
	print_r($res);
}
function testFactura()
{
	$factura = new Factura();
	$factura->codigo_documento_sector = 1;
	$factura->codigo_metodo_pago 	= 1;
	$factura->codigo_moneda			= 1;
	$factura->codigo_sucursal		= 0;
	$factura->complemento			= null;
	$factura->customer				= 'Miranda';
	$factura->discount				= 0;
	$factura->monto_giftcard		= 0;
	$factura->nit_ruc_nif			= '4898632';
	$factura->punto_venta			= 0;
	$factura->tipo_cambio			= 1;
	$factura->tipo_documento_identidad	= 1;
	$factura->tipo_factura_documento	= 1;
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
	$factura->items[] = $item;
	
	
	$api = instanceApi();
	try
	{
		$res = $api->crearFactura($factura);
		print_r($res);
	}
	catch(ExceptionApi $e)
	{
		print_r($e->response->json());
	}
	catch(Exception $e)
	{
		print $e->getMessage();
	}
}
function testAnularFactura()
{
	$api = instanceApi();
	try
	{
		$res = $api->anularFactura(13, 1);
		print_r($res);
	}
	catch(ExceptionApi $e)
	{
		print_r($e->response->json());
	}
	catch(Exception $e)
	{
		print $e->getMessage();
	}
}
```
