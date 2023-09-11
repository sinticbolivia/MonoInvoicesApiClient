<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

use Exception;

class MonoInvoicesApi
{
	public	$server;
	public 	$version = '1.0.13';
	public	$baseUrl;
	public	$token;
	public	$enableCache = false;
	public	$cacheDir = null;
	public	$lastResponse = null;
	
	public function __construct($srv)
	{
		$this->server = $srv;
		$this->baseUrl = $this->server . '/api';
	}
	public function login(string $username, string $pass)
	{
		$endpoint = $this->baseUrl . '/v1.0.0/users/get-token';
		$data = json_encode(['username' => $username, 'password' => $pass]);
		$req = new Request();
		$req->setHeaders(['Content-Type' => 'application/json']);
		$this->lastResponse = $res = $req->post($endpoint, $data);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de autenticacion', $res);
		$obj = $res->json();
		$this->token = $obj->data->token;
		
		return $obj;
	}
	public function validateToken()
	{
		if( !$this->token )
			throw new Exception('Token invalido, no se puede enviar la solicitud');
	}
	/**
	 * 
	 * @return Request
	 */
	protected function getRequest()
	{
		$headers = ['Content-Type' => 'application/json'];
		if( $this->token )
			$headers['Authorization'] = 'Bearer ' . $this->token;
		
		$req = new Request();
		$req->setHeaders($headers);
		
		return $req;
	}
	public function isCacheEnabled()
	{
		if( $this->enableCache && $this->cacheDir /*&& is_dir($this->cacheDir)*/ )
		{
			return true;
		}
		return false;
	}
	public function saveCache($filename, $data)
	{
		if( !$this->isCacheEnabled() )
			return false;
		file_put_contents($filename, json_encode($data));
	}
	public function loadCache($filename)
	{
		$daySecs = 86400;
		
		if( !is_file($filename) || filesize($filename) <= 0 )
			return null;
		if( !filectime($filename) || ( (time() - filectime($filename)) > $daySecs  ) )
			return null;
		$data = json_decode(file_get_contents($filename));
		return $data;
	}
	/**
	 * 
	 * @throws ExceptionApi
	 * @return object
	 */
	public function unidadesMedida()
	{
		$this->validateToken();
		$filename = sprintf("%s/unidades-medida.json", $this->cacheDir);
		
		if( $this->isCacheEnabled() && ($data = $this->loadCache($filename)) )
			return $data;
		
		$endpoint = $this->baseUrl . '/invoices/siat/v2/sync-unidades-medida';
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
		$this->saveCache($filename, $res->json());
		
		return $res->json();
	}
	/**
	 *
	 * @throws ExceptionApi
	 * @return object
	 */
	public function actividades()
	{
		$filename = sprintf("%s/actividades.json", $this->cacheDir);
		if( $this->isCacheEnabled() && ($data = $this->loadCache($filename)) )
			return $data;
		
		$this->validateToken();
		
		$endpoint = $this->baseUrl . '/invoices/siat/v2/actividades';
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
			
		$res = $res->json();
		if( is_object($res->data->RespuestaListaActividades->listaActividades) )
			$res->data->RespuestaListaActividades->listaActividades = [$res->data->RespuestaListaActividades->listaActividades];
		$this->saveCache($filename, $res);
		
		return $res;
	}
	/**
	 *
	 * @throws ExceptionApi
	 * @return object
	 */
	public function productosServicios()
	{
		$filename = sprintf("%s/productos-servicios.json", $this->cacheDir);
		if( $this->isCacheEnabled() && ($data = $this->loadCache($filename)) )
			return $data;
		
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/lista-productos-servicios';
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
		
		$this->saveCache($filename, $res->json());
		
		return $res->json();
	}
	/**
	 *
	 * @throws ExceptionApi
	 * @return object
	 */
	public function motivosAnulacion()
	{
		$filename = sprintf("%s/motivos-anulacion.json", $this->cacheDir);
		if( $this->isCacheEnabled() && ($data = $this->loadCache($filename)) )
			return $data;
		
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/sync-motivos-anulacion';
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
		
		$this->saveCache($filename, $res->json());
		
		return $res->json();
	}
	public function crearFactura(Factura $factura)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices';
		$data = json_encode($factura);
		$this->lastResponse = $res = $this->getRequest()->post($endpoint, $data);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error creando factura', $res);
		return $res->json();
	}
	public function anularFactura(int $id, int $codigoMotivo)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/' . $id . '/void';
		$data = json_encode(['motivo_id' => $codigoMotivo]);
		$this->lastResponse = $res = $this->getRequest()->post($endpoint, $data);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error anulando factura', $res);
		return $res->json();
	}
	public function obtenerFactura(int $id)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/invoices/' . $id;
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo factura', $res);
		return $res->json();
	}
	public function listadoEventos(int $sucursal = 0, int $puntoventa = 0, int $page = 1, int $limit = 25)
	{
		$filename = sprintf("%s/eventos.json", $this->cacheDir);
		if( $this->isCacheEnabled() && ($data = $this->loadCache($filename)) )
			return $data;
		
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/eventos?sucursal_id={$sucursal}&puntoventa_id={$puntoventa}&page={$page}&limit={$limit}";
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo Eventos', $res);
		
		$this->saveCache($filename, $res->json());
		
		return $res->json();
	}
	public function crearEvento(Evento $evento)
	{
		$this->validateToken();
		$endpoint 	= $this->baseUrl . '/invoices/siat/v2/eventos';
		$data 		= json_encode($evento);
		$this->lastResponse = $res 		= $this->getRequest()->post($endpoint, $data);
		
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error creando evento', $res);
		return $res->json();
	}
	public function cerrarEvento(int $id)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/eventos/'. $id .'/cerrar';
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error anulando factura', $res);
		return $res->json();
	}
	/**
	 * 
	 * @param int $id
	 * @param string $tpl Plantilla de la factura pagina|rollo
	 * @throws ExceptionApi
	 * @return mixed
	 */
	public function obtenerPdf(int $id, $tpl = null)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/'. $id .'/pdf';
		if( $tpl )
			$endpoint .= '?tpl=' . $tpl;
		
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo el PDF de la factura', $res);
		return $res->json();
	}
	/**
	 * 
	 * @param Cliente $cliente
	 * @throws ExceptionApi
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\Cliente
	 */
	public function crearCliente(Cliente $cliente)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/customers';
		$data = json_encode($cliente);
		$this->lastResponse = $res = $this->getRequest()->post($endpoint, $data);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error creando el cliente', $res);
		
		$cliente->bind($res->json());
		
		return $cliente;
	}
	/**
	 * 
	 * @param int $id
	 * @throws ExceptionApi
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\Cliente
	 */
	public function obtenerCliente(int $id)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/customers/' . $id;
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo el PDF de la factura', $res);
		$cliente = new Cliente();
		$cliente->bind($res->json());
		
		return $cliente;
	}
	public function tiposDocumentoIdentidad()
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/sync-documentos-identidad/';
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo el PDF de la factura', $res);
		
		return $res->json();
	}
	/**
	 * Obtener listado de facturas
	 * $args Es un array de argumentos aceptados
	 * 		keyword: Especifica la palabra clave para la busqueda
	 * 		date_init: Especifica la fecha inicial para intervalo de busqueda
	 * 		date_end: Especifica la fecha fin para intervalo de busqueda
	 * 
	 * @param number $page
	 * @param number $limit
	 * @param array $args
	 * @throws ExceptionApi
	 * @return Factura[]
	 */
	public function listadoFacturas($page = 1, $limit = 10, $args = [])
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . sprintf("/invoices/siat/v2/invoices?page=%d&limit=%d", $page, $limit);
		if( is_array($args) )
		{
			$endpoint .= '&' . http_build_query($args);	
		}
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo el listado de facturas', $res);
			
		return $res->json()->data;
	}
	public function validarNit($nit)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/validate-nit?nit=' . $nit;
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de validacion del NIT', $res);
			
		return $res->json()->data;
	}
	public function listadoCufds(int $sucursal = 0, int $puntoventa = 0, int $page = 1, int $limit = 25)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/cufds?sucursal_id={$sucursal}&puntoventa_id={$puntoventa}&page={$page}&limit={$limit}";
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo CUFDs', $res);
			
		return $res->json()->data;
	}
	public function listadoSucursales(int $page = 1, int $limit = 25)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/branches?page={$page}&limit={$limit}";
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo Sucursales', $res);
			
		return $res->json()->data;
	}
	public function listadoPuntosVenta(int $sucursal = 0, int $page = 1, int $limit = 25)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/puntos-venta?sucursal_id={$sucursal}&page={$page}&limit={$limit}";
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo Puntos Venta', $res);
			
		return $res->json()->data;
	}
	/**
	 *
	 * @param int $id
	 * @param string $tpl Plantilla de la factura pagina|rollo
	 * @throws ExceptionApi
	 * @return mixed
	 */
	public function obtenerHtml(int $id, $tpl = null)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/'. $id .'/html';
		if( $tpl )
			$endpoint .= '?tpl=' . $tpl;
			
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo el HTML de la factura', $res);
		return $res->json();
	}
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @throws ExceptionApi
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\RequestResponse
	 */
	public function listadoClientes(int $page = 1, int $limit = 50)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/customers?page=$page&limit=$limit";
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo listado de clientes', $res);
		return $res;
	}
	/**
	 * Obtiene el listado de productos
	 * 
	 * @param int $page
	 * @param int $limit
	 * @throws ExceptionApi
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\RequestResponse
	 */
	public function listadoProductos(int $page = 1, int $limit = 50)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/products?page=$page&limit=$limit";
		$this->lastResponse = $res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo listado de productos', $res);
		return $res->json()->data;
	}
}