<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

use Exception;

class MonoInvoicesApi
{
	public	$server;
	public 	$version = '1.0.3';
	public	$baseUrl;
	public	$token;
	
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
		$res = $req->post($endpoint, $data);
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
	/**
	 * 
	 * @throws ExceptionApi
	 * @return object
	 */
	public function unidadesMedida()
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/sync-unidades-medida';
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
		
		return $res->json();
	}
	/**
	 *
	 * @throws ExceptionApi
	 * @return object
	 */
	public function actividades()
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/actividades';
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
			
		$res = $res->json();
		if( is_object($res->data->RespuestaListaActividades->listaActividades) )
			$res->data->RespuestaListaActividades->listaActividades = [$res->data->RespuestaListaActividades->listaActividades];
		return $res;
	}
	/**
	 *
	 * @throws ExceptionApi
	 * @return object
	 */
	public function productosServicios()
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/lista-productos-servicios';
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
			
		return $res->json();
	}
	/**
	 *
	 * @throws ExceptionApi
	 * @return object
	 */
	public function motivosAnulacion()
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/sync-motivos-anulacion';
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de catalogo', $res);
			
		return $res->json();
	}
	public function crearFactura(Factura $factura)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices';
		$data = json_encode($factura);
		$res = $this->getRequest()->post($endpoint, $data);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error creando factura', $res);
		return $res->json();
	}
	public function anularFactura(int $id, int $codigoMotivo)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/' . $id . '/void';
		$data = json_encode(['motivo_id' => $codigoMotivo]);
		$res = $this->getRequest()->post($endpoint, $data);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error anulando factura', $res);
		return $res->json();
	}
	public function obtenerFactura(int $id)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/invoices/' . $id;
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo factura', $res);
		return $res->json();
	}
	public function listadoEventos(int $sucursal = 0, int $puntoventa = 0, int $page = 1, int $limit = 25)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/eventos?sucursal_id={$sucursal}&puntoventa_id={$puntoventa}&page={$page}&limit={$limit}";
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo Eventos', $res);
			
		return $res->json()->data;
	}
	public function crearEvento(Evento $evento)
	{
		$this->validateToken();
		$endpoint 	= $this->baseUrl . '/invoices/siat/v2/eventos';
		$data 		= json_encode($evento);
		$res 		= $this->getRequest()->post($endpoint, $data);
		
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error creando evento', $res);
		return $res->json();
	}
	public function cerrarEvento(int $id)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/eventos/'. $id .'/cerrar';
		$res = $this->getRequest()->get($endpoint);
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
		
		$res = $this->getRequest()->get($endpoint);
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
		$res = $this->getRequest()->post($endpoint, $data);
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
		$res = $this->getRequest()->get($endpoint);
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
		$res = $this->getRequest()->get($endpoint);
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
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obtiendo el listado de facturas', $res);
			
		return $res->json()->data;
	}
	public function validarNit($nit)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . '/invoices/siat/v2/validate-nit?nit=' . $nit;
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error de validacion del NIT', $res);
			
		return $res->json()->data;
	}
	public function listadoCufds(int $sucursal = 0, int $puntoventa = 0, int $page = 1, int $limit = 25)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/cufds?sucursal_id={$sucursal}&puntoventa_id={$puntoventa}&page={$page}&limit={$limit}";
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo CUFDs', $res);
			
		return $res->json()->data;
	}
	public function listadoSucursales(int $page = 1, int $limit = 25)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/branches?page={$page}&limit={$limit}";
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo Sucursales', $res);
			
		return $res->json()->data;
	}
	public function listadoPuntosVenta(int $sucursal = 0, int $page = 1, int $limit = 25)
	{
		$this->validateToken();
		$endpoint = $this->baseUrl . "/invoices/siat/v2/puntos-venta?sucursal_id={$sucursal}&page={$page}&limit={$limit}";
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo Puntos Venta', $res);
			
		return $res->json()->data;
	}
}