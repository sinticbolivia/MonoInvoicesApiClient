<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

class Factura extends SBObject
{
	const	FACTURA_CON_CREDITO_FISCAL = 1;
	const	FACTURA_SIN_CREDITO_FISCAL = 2;
	
	public	$invoice_id;
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
	public	$subtotal;
	public	$total;
	public	$invoice_number;
	public	$control_code;
	public	$invoice_date_time;
	public	$status;
	public	$cufd;
	public	$cuf;
	public	$evento_id;
	public	$tipo_emision;
	public	$nit_emisor;
	public	$siat_id;
	public	$leyenda;
	public	$siat_url;
	public	$data;
	
	/**
	 * @var FacturaItem[]
	 */
	public	$items = [];
	
	public function __construct()
	{
		
	}
	
}


