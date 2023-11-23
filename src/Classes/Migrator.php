<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

use SinticBolivia\SBFramework\Classes\SB_Factory;
use SinticBolivia\SBFramework\Modules\Customers\Entities\Customer;
use SinticBolivia\SBFramework\Modules\Invoices\Entities\Product;
use SinticBolivia\SBFramework\Modules\Invoices\Entities\Invoice;
use Exception;

class Migrator
{
	protected	$api;
	protected	$server;
	protected	$username;
	protected	$pwd;
	
	public function __construct($servidor, $usuario, $pwd)
	{
		$this->server 	= $servidor;
		$this->username = $usuario;
		$this->pwd 		= $pwd;
	}
	public function init()
	{
		if( !class_exists(SB_Factory::class) )
			throw new Exception('No puede realizar la migracion, es necesario SBFramework');
		
		$this->api = new MonoInvoicesApi($this->server);
		$this->api->login($this->username, $this->pwd);
	}
	public function migrate(int $destUserId)
	{
		set_time_limit(0);
		$this->migrateCustomers($destUserId);
		$this->migrateProducts($destUserId);
		$this->migrateInvoices($destUserId);
	}
	public function migrateCustomers(int $destUserId)
	{
		print "MIGRANDO CLIENTES\n";
		$page = 1;
		$limit = 50;
		$res = $this->api->listadoClientes($page, $limit);
		$total_pages = $res->getHeader('Total-Pages', 1);
		$total_rows = $res->getHeader('Total-Rows', 1);
		echo "TOTAL CLIENTES: {$total_rows}\n";
		$this->persistCustomers($res->json()->data, $destUserId);
		if( $total_pages > 1 )
		{
			for($i = 2; $i <= $total_pages; $i++)
			{
				$res = $this->api->listadoClientes($i, $limit);
				$this->persistCustomers($res->json()->data, $destUserId);
			}
		}
		
		print "MIGRACION USUARIOS FINALIZADA\n";
	}
	public function persistCustomers($customers, int $destUserId)
	{
		$dbh = SB_Factory::getDbh();
		//$params = $dbh->FetchResults("SELECt * FROM parameters");
		//print_r($params);
		foreach($customers as $cust)
		{
			$lcust = Customer::GetBy('extern_id', $cust->customer_id, true);
			if( $lcust )
				continue;
			$customer = (array)$cust;
			$customer['extern_id'] = $cust->customer_id;
			$customer['user_id'] = $destUserId;
			$meta = $cust->meta;
			unset($customer['customer_id'], $customer['last_modificacion_data'], $customer['creation_date'], $customer['meta']);
			echo "MIGRANDO CLIENTE: {$cust->last_name}\n";
			$dbh->Insert('mb_customers', $customer);
		}
	}
	public function migrateProducts(int $destUserId)
	{
		print "MIGRANDO PRODUCTOS...\n";
		$limit			= 50;
		$products 		= $this->api->listadoProductos(1, $limit);
		//print_r($this->api->lastResponse->rawBody);
		$total_pages	= $this->api->lastResponse->getHeader('Total-Pages', 1);
		$total_rows		= $this->api->lastResponse->getHeader('Total-Items', 1);
		echo "TOTAL PRODUCTOS: {$total_rows}\n";
		$this->persistProducts($products, $destUserId);
		if( $total_pages > 1 )
		{
			for($i = 2; $i <= $total_pages; $i++)
			{
				$products = $this->api->listadoProductos($i, $limit);
				$this->persistProducts($products, $destUserId);
			}
		}
		print "MIGRACION DE PRODUCTOS TERMINADA\n";
	}
	protected function persistProducts(array $products, int $destUserId)
	{
		$dbh = SB_Factory::getDbh();
		foreach($products as $prod)
		{
			$lprod = Product::GetBy('extern_id', $prod->id, true);
			if( $lprod )
				continue;
			echo "MIGRANDO PRODUCTO: {$prod->product_name}\n";
			$mprod = (array)$prod;
			$mprod['extern_id'] = $prod->id;
			$mprod['user_id'] 	= $destUserId;
			$mprod['last_modification_date'] = $mprod['creation_date'] = date('Y-m-d H:i:s');
			unset($mprod['id']);
			//print_r($mprod);
			$dbh->Insert('mb_invoice_products', $mprod);
		}
	}
	public function migrateInvoices(int $destUserId)
	{
		echo "MIGRANDO FACTURAS\n";
		$page 			= 1;
		$limit			= 100;
		$invoices 		= $this->api->listadoFacturas($page, $limit);
		$total_pages 	= $this->api->lastResponse->getHeader('Total-Pages', 1);
		$total_rows 	= $this->api->lastResponse->getHeader('Total-Items', 1);
		//print_r($this->api->lastResponse->rawBody);die;
		//print_r($res);
		echo "TOTAL FACTURAS: {$total_rows}\n";
		$this->persistInvoices($invoices, $destUserId);
		if( $total_pages > 1 )
		{
			for($i = 2; $i <= $total_pages; $i++)
			{
				$invoices = $this->api->listadoFacturas($i, $limit);
				$this->persistInvoices($invoices, $destUserId);
			}
		}
		print "MIGRACION DE FACTURAS COMPLETADA\n";
		//print "TOTAL FACTURAS MIGRADAS: $total_rows\n";
	}
	protected function persistInvoices(array $invoices, int $destUserId)
	{
		$dbh = SB_Factory::getDbh();
		
		foreach($invoices as $invoice)
		{
			//print_r($invoice);die;
			
			$customer = Customer::GetBy('extern_id', $invoice->customer_id, true);
			if( !$customer )
				continue;
			$linvoice = Invoice::GetBy('extern_id', $invoice->invoice_id, true);
			if( $linvoice )
				continue;
			print "Migration invoice ID: {$invoice->invoice_id}\n";
			$meta = $invoice->meta;
			$minvoice = (array)$invoice;
			$minvoice['extern_id'] 		= $invoice->invoice_id;
			$minvoice['customer_id'] 	= $customer->customer_id;
			$minvoice['user_id']		= $destUserId;
			$minvoice['last_modification_date'] = $minvoice['creation_date'] = date('Y-m-d H:i:s');
			$minvoice['data'] = (is_object($invoice->data) || is_array($invoice->data)) ? json_encode($invoice->data) : $invoice->data;
			//print_r($minvoice);die;
			unset($minvoice['invoice_id'], $minvoice['siat_url'], $minvoice['print_url'], $minvoice['sector'], $minvoice['leyenda'], $minvoice['meta'], $minvoice['items']);
			$id = $dbh->Insert('mb_invoices', $minvoice);
			if( $id <= 0 )
				continue;
			
			foreach($invoice->items as $item)
			{
				$mitem = (array)$item;
				unset($mitem['item_id']);
				$mitem['last_modification_date'] = $mitem['creation_date'] = date('Y-m-d H:i:s');
				$mitem['invoice_id'] = $id;
				$mitem['data'] = (is_object($item->data) || is_array($item->data)) ? json_encode($item->data) : $item->data;
				if( $item->product_id > 0 )
				{
					$lprod = Product::GetBy('extern_id', $item->product_id, true);
					$mitem['product_id'] = $lprod->id;
				}
				$dbh->Insert('mb_invoice_items', $mitem);
			}
			
		}
	}
}