<?php

/**
 * PDO wrapper class
 */
 
class Db {

	/**
	 * The handle to the database connection
	 */
	private static $_conn = null;
	
	/**
	 * The value of the last error message
	 */
	public static $lastError = '';
	
	/**
	 * Opens a connection to the database
	 */
	public static function Connect($dsn, $user = '', $pass = '')
	{
		$retVal = false;
		
		try {
			self::$_conn = new PDO($dsn, $user, $pass);
			$retVal = true;
		} catch (PDOException $e) {
			echo $e->getMessage();
			self::$lastError = $e->getMessage();
		}
		
		return $retVal;
	}

	/**
	 * Executes a query
	 */
	public static function Query($sql, $params = null)
	{
		$retVal = null;
		
		try {
			$comm = self::$_conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$comm->execute($params);
			switch (strtolower(substr($sql, 0, 6))) {
				case 'select':
					$retVal = new stdClass();
					$retVal->count = $comm->rowCount();
					$retVal->comm = $comm;
					break;
				case 'insert':
					$retVal = self::$_conn->lastInsertId();
					break;
				case 'update':
				case 'delete':
					$retVal = $comm->rowCount();
					break;
			}
			
		} catch (PDOException $e) {
			self::$lastError = $e->Message();
		}
		
		
		return $retVal;
	}
	
	/**
	 * Fetches the next row in a record set
	 */
	public static function Fetch($rs)
	{
		$retVal = null;
		
		if (is_object($rs) && null != $rs->comm) {
			$retVal = $rs->comm->fetchObject();
		}
		
		return $retVal;
	}
	
}