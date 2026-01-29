<?php

class Request
{

	public $id_prestation;
	public $action;
	public $SmscId;
	public $Content;

	private $_Str;

	public function __construct()
	{

		$this->id_prestation = GetParameter::FromArray($_REQUEST, 'id');
		$this->action = GetParameter::FromArray($_REQUEST, 'etat');
		$this->SmscId = GetParameter::FromArray($_REQUEST, 'smscid');
		$this->Content = GetParameter::FromArray($_REQUEST, 'Content');
		$this->_Str = 'action =' . $this->action ;
	}

	public function __toString()
	{
		return $this->_Str;
	}
}
