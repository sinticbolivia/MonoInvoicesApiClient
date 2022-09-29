<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

class Factura
{
	public 	$customer_id;
	public 	$customer;
	public	$nit_ruc_nif;
	public	$discount;
	public	$monto_giftcard = 0;
	public	$codigo_sucursal;
	public	$punto_venta;
	public	$codigo_documento_sector;
	public	$tipo_documento_identidad;
	public	$codigo_metodo_pago = 28;
	public	$codigo_moneda = 1;
	public	$tipo_cambio = 1;
	public	$complemento;
	public	$numero_tarjeta;
	public	$tipo_factura_documento = 1;
	/**
	 * @var FacturaItem[]
	 */
	public	$items = [];
	
}


