<?php
namespace SinticBolivia\MonoInvoicesApi\Classes;

class SBObject
{
	public function bind($data)
	{
		if( !is_array($data) && !is_object($data) )
			return false;
		foreach($data as $prop => $val)
		{
			if( !property_exists($this, $prop) )
				continue;
			$this->$prop = $val;
		}
	}
}