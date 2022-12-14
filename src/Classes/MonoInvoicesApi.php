<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

use Exception;

class MonoInvoicesApi
{
	public	$server;
	public 	$version = '1.0.0';
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
			
		return $res->json();
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
		$endpoint = '/invoices/siat/v2/invoices/' . $id;
		$res = $this->getRequest()->get($endpoint);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error obteniendo factura', $res);
		return $res->json();
	}
	public function crearEvento(Evento $evento)
	{
		$this->validateToken();
		$endpoint 	= '/invoices/siat/v2/eventos';
		$data 		= json_encode($evento);
		$res 		= $this->getRequest()->post($endpoint, $data);
		
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error creando evento', $res);
		return $res->json();
	}
	public function cerrarEvento(int $id)
	{
		$this->validateToken();
		$endpoint = '/invoices/siat/v2/eventos/'. $id .'/cerrar';
		$res = $this->getRequest()->get($endpoint, $data);
		if( $res->statusCode != 200 )
			throw new ExceptionApi('Error anulando factura', $res);
		return $res->json();
	}
}