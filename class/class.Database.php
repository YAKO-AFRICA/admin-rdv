<?php

class Database
{

	private $_Instance;
	public $ErrorCode = 0;
	public $ErrorMessage = NULL;

	public $Logger = NULL;


	public function __construct(Logger $logger)
	{
		$this->Logger = $logger;
		$this->_Connect();
	}

	public function Close()
	{
		$this->_Instance = NULL;
	}

	public function Update($sql, array $boundParameters = NULL)
	{

		//echo $sql;
		//print_r($boundParameters);		//exit;
		$this->Logger->Handler(__class__ . '.' . __function__, 'executing query[' . $sql . '], boundParameters[' . json_encode($boundParameters) . ']');
		$return = array();
		$this->_SetErrors(0, NULL);
		try {
			$stmt = $this->_Instance->prepare($sql);
			$stmt->execute($boundParameters);
			$return['AffectedRows'] = $stmt->rowCount();
			$return['LastInsertId'] = $this->_Instance->lastInsertId();
			return $return;
		} catch (PDOException $e) {
			//print_r($e);
			$this->Logger->Handler(__class__ . '.' . __function__, 'failed to execute the query, errorCode=' . $e->getCode() . ', errorMessage=' . $e->getMessage());
			$this->_SetErrors($e->getCode(), $e->getMessage());
			return NULL;
		}
	}

	public function Select($sql, array $boundParameters = NULL)
	{
		//echo $sql;print_r($boundParameters);		//exit;
		$this->Logger->Handler(__class__ . '.' . __function__, 'executing query[' . $sql . '], boundParameters[' . json_encode($boundParameters) . ']');
		$this->_SetErrors(0, NULL);
		try {
			$stmt = $this->_Instance->prepare($sql);
			$stmt->execute($boundParameters);
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		} catch (PDOException $e) {
			$this->Logger->Handler(__class__ . '.' . __function__, 'failed to execute the query -> errorCode=' . $e->getCode() . ', errorMessage=' . $e->getMessage());
			$this->_SetErrors($e->getCode(), $e->getMessage());
			return NULL;
		}
	}

	/*
	private function _Connect() {
		$this->Logger->Handler(__class__.'.'.__function__, 'trying to connect to the database');
		try {
			$this->_Instance = new PDO('mysql:host='.Config::DatabaseHost.';dbname='.Config::DatabaseName, Config::DatabaseUser, Config::DatabasePass);
			$this->_Instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} 
		catch(PDOException $e) {
			$this->Logger->Handler(__class__.'.'.__function__, 'failed to connect to the database -> errorCode='.$e->getCode().', errorMessage='.$e->getMessage());
			$this->_SetErrors($e->getCode(), $e->getMessage());
		}
	}
	*/

	private function _Connect()
	{
		$this->Logger->Handler(__CLASS__ . '.' . __FUNCTION__, 'trying to connect to the database');
		try {
			$this->_Instance = new PDO(
				'mysql:host=' . Config::DatabaseHost . ';dbname=' . Config::DatabaseName . ';charset=utf8mb4',
				Config::DatabaseUser,
				Config::DatabasePass,
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
				]
			);
		} catch (PDOException $e) {
			$this->Logger->Handler(__CLASS__ . '.' . __FUNCTION__, 'failed to connect to the database -> errorCode=' . $e->getCode() . ', errorMessage=' . $e->getMessage());
			$this->_SetErrors($e->getCode(), $e->getMessage());
		}
	}


	private function _SetErrors($code, $message)
	{
		if ($message != NULL) $this->Logger->Handler(__class__ . '.' . __function__, 'code=' . $code . ', message=' . $message);
		$this->ErrorCode = $code;
		$this->ErrorMessage = $message;
	}
}
