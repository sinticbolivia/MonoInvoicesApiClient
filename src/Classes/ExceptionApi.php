<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

use Exception;

class ExceptionApi extends Exception
{
	/**
	 * @var RequestResponse
	 */
	public	$response;
	
	public function __construct($message, RequestResponse $response, $code = null)
	{
		parent::__construct($message, $code);
		$this->response = $response;
		
	}
}