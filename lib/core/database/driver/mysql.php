<?php

/**
 * @author Administrator
 *MYSQL驱动类
 */
class MysqlObject extends DataBaseObject {
	
	protected function __construct($configobj) {
		
		$config ['dbhost'] = isset ( $configobj->dbhost ) ? $configobj->dbhost : 'localhost';
		$config ['dbuser'] = isset ( $configobj->dbuser ) ? $configobj->dbuser : '';
		$config ['dbprefix'] = isset ( $configobj->dbprefix ) ? $configobj->dbprefix : '';
		$config ['dbpassword'] = isset ( $configobj->dbpassword ) ? $configobj->dbpassword : '';
		$config ['dbdatabase'] = isset ( $configobj->dbdatabase ) ? $configobj->dbdatabase : '';
		
		if (! function_exists ( 'mysql_connect' )) {
			
			$this->errorNum = 1;
			$this->errorMsg = '数据库模块未开启';
			
			throw new DbException ( $this->errorMsg );
		}
		
		if (! ($this->connection = @mysql_connect ( $config ['dbhost'], $config ['dbuser'], $config ['dbpassword'], true ))) {
			
			$this->errorMsg = '数据库连接失败';
			throw new DbException ( $this->errorMsg );
		}
		
		parent::__construct ( $config );
		if (! empty ( $config ['dbdatabase'] )) {
			
			$this->selectDb ( $config ['dbdatabase'] );
		}
	
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct()
	{
		if (is_resource($this->connection))
		{
			mysql_close($this->connection);
		}
	}
	/**
	 * 选择数据库
	 *
	 * @param $database string
	 *       	 数据库名称
	 * @throws DbException
	 * @return boolean 如果$database为空返回false
	 */
	public function selectDb($database_name) {
		if (empty ( $database_name )) {
			return false;
		}
		
		if (! mysql_select_db ( $database_name,$this->connection)) {
			
			$this->errorMsg = '数据库不存在';
			throw new DbException ( $this->errorMsg );
		}
	
	}
	
	/**
	 *
	 * @param $sqlstr string
	 *       	 查询数据库字符串
	 * @throws DbException 错误异常
	 * @return resource 返回ressouce类型并给对象结果集赋值
	 */
	public function query($sqlstr) {
		
		if (! is_resource ( $this->connection )) {
			
			throw new DbException ( '数据库查询(query)连接失败' );
		
		}
		$this->free ();
		$this->queryresource = mysql_query ( $sqlstr, $this->connection );
		
		if(!$this->queryresource){
			
			$this->errorNum=(int)mysql_errno($this->connection);
			$this->errorMsg=(string)mysql_error($this->connection);
			echo  "<font color=\"#FF0000\">$sqlstr</font>".'<br/>';
			exit($this->errorMsg);
		}else{
			return $this->queryresource;
		}
	}
	
	protected  function setUTF() {
		return mysql_query ( "SET NAMES 'utf8'", $this->connection );
	
	}
	
	/*
	 * 对结果集进行数组关联
	 */
	public function fetch_assoc($query_resource=null) {
			return mysql_fetch_assoc ($query_resource?$query_resource:$this->queryresource);
	
	}
	
	/*
	 * 执行增，删，改操作 @return number 影响的行数
	 */
	public function execute($sqlstr) {
		
		if (! empty ( $sqlstr )) {
			
			$rs = $this->query ( $sqlstr );
			
			// 取得最近一次与rs关联的 INSERT，UPDATE 或 DELETE 查询所影响的记录行数。
			$this->affected_row = mysql_affected_rows ();
			// 取得上一步 INSERT 操作产生的 ID
			$this->insert_id = mysql_insert_id ();
			
			
			
			
		}else{
			
			return false;
		}
		
	}
	
	public function free() {
		if(is_resource($this->queryresource)&&!empty($this->queryresource)){
			mysql_free_result ( $this->queryresource );
		}
		
	
	}
	
	public function close() {
		if (! empty ( $this->queryresource )) {
			
			$this->free ();
		}
		if (! empty ( $this->connection )) {
			mysql_close ( $this->connection );
		}
	
	}
	
	public function rs_num_row($resource=null) {
				
			return mysql_num_rows ( $resource?$resource:$this->queryresource );
	}
	
	public function rs_num_field($resource=null) {
	
			return mysql_num_fields ( $resource?$resource:$this->queryresource  );
	
	}
	public function getAffectedRows(){
		
		return $this->affected_row;
	}
	public function insert_id(){
		
		return $this->insert_id;
	}

}
?>