<?php
/**
* MySQL Database Wrapper class
*/
class MySQLDb
{
	/**
	* MySQL Connection resource link
	*
	* var resource
	*/
	private $slink;
	
	/**
	* MySQL result
	*
	* var result
	*/
	private $result;
	
	/**
	* Keeps track of the last query
	*
	* var string
	*/
	private $lastQuery;
	
	/**
	* Database host
	*
	* var string
	*/
	private $dbHost;

	/**
	* Database username
	*
	* var string
	*/
	private $dbUser;

	/**
	* Database password
	*
	* var string
	*/
	private $dbPass;

	/**
	* Database name.
	*
	* var string
	*/
	private $dbName;
	
	/**
	* Constructor. Initializes a new database connection and selects the database.
	*
	* param  string   $db_host  Database host
	* param  string   $db_user  Database username
	* param  string   $db_pass  Database password
	* param  string   $db_name  Database name
	* return resource   Mysql link resource.
	*/
	public function __construct($db_host, $db_user, $db_pass, $db_name)
	{
		$this->dbHost = $db_host;
		$this->dbUser = $db_user;
		$this->dbPass = $db_pass;
		$this->dbName = $db_name;
		$this->slink = null;
		$this->result = false;
		
		$this->slink = mysql_connect($this->dbHost, $this->dbUser, $this->dbPass);
		
		if ( $this->slink === false )
		{
			throw new Exception("Não foi possível conectar ao banco de dados. 0x01");
		}
		
		if (! mysql_select_db($this->dbName, $this->slink) )
		{
			throw new Exception("O banco de dados `{$this->dbName}` não foi encontrado. 0x02");
		}
	}
	
	/**
	* Destructor. Closes the active connection.
	*
	*/
	public function __destruct()
	{
		mysql_close($this->slink);
	}
	
	/**
	* Executes a sql query. Returns the mysql result or false on failure.
	*
	* param  string  SQL Query
	* return result
	*/
	public function Query($query)
	{
		if (! $this->result = mysql_query($query, $this->slink) )
		{
			return false;
		}
		else
		{
			return $this->result;
		}
	}
	
	/**
	* Fetches a single array based on a sql query. Equal to Query() but returns an array instead of a result.
	* Returns false on failure.
	*
	* param  string  SQL Query
	* param  string  Optional flag
	* return mixed   Array or false
	*/
	public function FetchSingle($query, $flag = MYSQL_ASSOC)
	{
		return mysql_fetch_array($this->Query($query), $flag);
	}
	
	/**
	* Fetches an array based on a sql query. Equal to Query() but returns an array instead of a result.
	* Returns false on failure.
	*
	* param  string  SQL Query
	* param  string  Optional flag
	* return mixed   Array or false
	*/
	public function FetchFull($query, $flag = MYSQL_ASSOC)
	{
		$tmp_array = array();
		$res = $this->Query($query, $this->slink);
		
		while($tmp_array[] = mysql_fetch_array($res))
		{
			// Fill array
		}
		
		return $tmp_array;
	}
	
	/**
	* Returns the last inserted id on the database
	*
	* return int
	*/
	public function GetLastInsertId()
	{
		return (int) mysql_insert_id($this->slink);
	}
	
	/**
	* Returns the last error thrown by MySQL
	*
	* return string
	*/
	public function GetLastError()
	{
		return mysql_error($this->slink);
	}
}