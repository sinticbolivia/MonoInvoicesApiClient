<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

//use Exception;

class Request
{
	protected	$proxyIp 	= '187.130.139.197';
	protected	$proxyPort 	= 37812;
	protected	$proxyType 	= 'socks4';
	
	protected	$useProxy	= false;
	protected	$cookiesFile = null;
	protected	$timeout		= 20;
	/**
	 * @var Cookie[]
	 */
	protected	$cookies		= [];
	protected	$saveCookies	= false;
	protected	$headers		= [];
	
	public function __construct()
	{
		
	}
	/**
	 * 
	 * @param array $headers
	 * @param boolean $indexed
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\Request
	 */
	public function setHeaders(array $headers, $indexed = true)
	{
		$this->headers = [];
		if( !$indexed )
		{
			$this->headers = $headers;
			return $this;
		}
		foreach($headers as $key => $val)
		{
			$this->headers[] = sprintf("%s: %s", $key, $val);
		}
		return $this;
	}
	/**
	 *
	 * @param string $url
	 * @param mixed $data
	 * @param string $method
	 * @param array $headers
	 * @return RequestResponse
	 */
	public function request(string $url, $data = null, $method = 'GET', $headers = null)
	{
		$options = [
			CURLOPT_HEADER			=> 1,
			CURLOPT_RETURNTRANSFER 	=> 1,
			CURLOPT_FOLLOWLOCATION	=> 1,
			CURLOPT_MAXREDIRS		=> 100,
			CURLOPT_USERAGENT		=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:93.0) Gecko/20100101 Firefox/93.0',
			//CURLOPT_SSL_VERIFYHOST	=> 0,
			CURLOPT_TIMEOUT			=> $this->timeout,
			//CURLOPT_VERBOSE			=> true,
			//CURLINFO_HEADER_OUT		=> true,
			CURLOPT_SSL_VERIFYPEER	=> 0,
			CURLOPT_ENCODING		=> true
		];
		if( $this->useProxy )
			$options[CURLOPT_PROXY]	= sprintf("%s://%s:%d", $this->proxyType, $this->proxyIp, $this->proxyPort);
			
		if( in_array($method, ['PUT', 'POST']) )
		{
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_POSTFIELDS] = is_array($data) || is_object($data) ? http_build_query($data) : $data;
			//echo '<pre>', print_r($options[CURLOPT_POSTFIELDS], 1), '</pre>';
		}
		if( in_array($method, ['PUT', 'DELETE', 'PATCH']) )
			$options[CURLOPT_CUSTOMREQUEST] = $method;
		
		
		if( $headers && is_array($headers) )
		{
			$this->headers = array_merge($this->headers, $headers);
		}
		if( count($this->headers) )
		{
			//echo 'Sending headers', "\n";print_r($this->headers);
			$options[CURLOPT_HTTPHEADER] = $this->headers;
		}
		//##store cookies
		//Create And Save Cookies
		if( $this->cookiesFile )
		{
			$options[CURLOPT_COOKIEJAR]		= $this->cookiesFile;
			$options[CURLOPT_COOKIEFILE] 	= $this->cookiesFile;
		}
		if( $this->saveCookies )
		{
			$this->restoreCookies($options);
		}
		//echo 'Endpoint: ', $url, "\n";
		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$res = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		//var_dump('RES', $res);print_r($info);
		$response = new RequestResponse($res, $info);
		$response->statusCode = (int)$info['http_code'];
		return $response;
	}
	/**
	 * 
	 * @param string $url
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\RequestResponse
	 */
	public function get($url)
	{
		return $this->request($url, null, 'GET');
	}
	/**
	 * 
	 * @param string $url
	 * @param string $data
	 * @param array $headers
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\RequestResponse
	 */
	public function post($url, $data, $headers = null)
	{
		return $this->request($url, $data, 'POST', $headers);
	}
	/**
	 * 
	 * @param string $url
	 * @param array $headers
	 * @return \SinticBolivia\MonoInvoicesApi\Classes\RequestResponse
	 */
	public function delete($url, $headers = null)
	{
		return $this->request($url, null, 'DELETE');
	}
}
