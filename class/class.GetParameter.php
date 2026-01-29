<?php

class GetParameter 
{

	public static function FromArray(array $array, $parameterName, $defaultValue = '') 
	{
		return isset($array[$parameterName])? trim(strip_tags($array[$parameterName])): $defaultValue;
	}
}
