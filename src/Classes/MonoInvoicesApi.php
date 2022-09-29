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
			throw new ExceptionApi('Error de atenticacion', $res);
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
			$headers['Athorization'] = 'Bearer ' . $this->token;
		
		$req = new Request();
		$req->setHeaders($headers);
		
		return $res;
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
	public function crearFactura()
	{
		
	}
}