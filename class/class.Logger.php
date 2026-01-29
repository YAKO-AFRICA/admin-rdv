<?php

class Logger 
{

	private $_Details;
	
	public function __construct($details) 
	{
		$this->_Details = $details;
	}
	
	public function Handler($type, $message) 
	{
		//print $message.PHP_EOL;
		@file_put_contents(Config::LogDirectory.'star-kids-afrique.log', date('Y-m-d H:i:s').'|'.$type.'|'.$this->_Details.': '.$message.PHP_EOL, FILE_APPEND);
	}
}
