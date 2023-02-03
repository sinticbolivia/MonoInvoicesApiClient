<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

class Cliente extends SBObject
{
	public		$customer_id;
	public		$code;
	public		$first_name;
	public		$last_name;
	public		$identity_document;
	public		$phone;
	public		$email;
	public		$address_1;
	public		$meta = [];
	
	public function __construct()
	{
		
	}
	public function setNitRucNif($nit_ruc_nif)
	{
		$this->setMeta('_nit_ruc_nif', $nit_ruc_nif);
	}
	public function setNombreFacturacion($nombre)
	{
		$this->setMeta('_billing_name', $nombre);
	}
	public function getNitRucNif()
	{
		return $this->getMeta('_nit_ruc_nif');
	}
	public function getNombreFacturacion()
	{
		return $this->getMeta('_billing_name');
	}
	/**
	 * Asigna datos adicionales para el cliente
	 * 
	 * @param string $key nombre del dato
	 * @param mixed $value valor del dato
	 */
	public function setMeta($key, $value)
	{
		$this->meta[$key] = $value;
	}
	public function getMeta($key, $defVal = null)
	{
		if( !isset($this->meta[$key]) )
			return $defVal;
		
		return $this->meta[$key];
	}
}