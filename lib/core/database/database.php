<?php

/**
 * @author Administrator
 *
 */
abstract class DataBaseObject {
	
	protected static $DBO = NULL;
	protected $dbprefix = null;
	protected $errorNum = 0;
	protected $errorMsg = '';
	protected $connection = null;
	protected $queryresource = null;
	protected $sqlstr = '';
	protected $affected_row=0;//execute操作影响行数
	protected $insert_id=0;//insert操作影响id

	
	protected  function __construct($config) {
		$this->dbprefix = $config ['dbprefix'];
		$this->errorNum = 0;
		$this->errorMsg = '';
		$this->setUTF ();
	
	}
	
	public static function getInstance($configobj) {
		if (empty ( self::$DBO )) {
			
			$driver = $configobj->dbdriver;
			$file = dirname ( __FILE__ ) . '/driver/' . $driver . '.php';
			
			if (file_exists ( $file )) {
				
				require_once $file;
			} else {
				
				exit ( '数据库驱动不存在' );
			}
			
			$classname = ucfirst ( $driver ) . 'Object';
			
			try {
				$dbobj = new $classname ( $configobj );
			} catch ( DbException $e ) {
				
				exit ( $e->getMessage () );
			}
			
			self::$DBO = $dbobj;
		}
		return self::$DBO;
	}
	
	public function table($tablename) {
		
		if (! empty ( $tablename )) {
			
			return $this->dbprefix .'_'. $tablename;
		}
	
	}
	
	/**
	 * 
	 *
	 * @param $sql string 查询语句
	 *      
	 * @return array 数组集合
	 */
	public function query_list($sql) {
		$list = array ();
		$queryrs = $this->query ( $sql );
		
		if (is_resource ( $this->queryresource )) {
			
			while ( $row = $this->fetch_assoc () ) {
				
				$list [] = $row;
			}
		
		}
		
		return $list;
	}
	
	
	public function insert($tablename, $array, $retrunid = 1) {
		$col = $values = $str = '';
		foreach ( $array as $key => $value ) {
				
			$col .= $str . $key ;
			$values .= $str . '\'' . $value . '\'';
			$str = ', ';
		}
		echo $sql = "INSERT INTO {$this->table($tablename)} ($col) VALUES($values)";
		$this->execute ( $sql );
		if ($retrunid) {
			return $this->insert_id();
	
		}
	
	}
	public function update($tablename, $array, $wheresqlarr) {
		$setsql = $comma = '';
		foreach ( $array as $key => $value ) {
				
			$setsql .= $comma . '`' . $key . '`' . '=\'' . $value . '\'';
			$comma = ', ';
		}
		$where = $comma = '';
		if (empty ( $wheresqlarr )) {
			$where = '1';
		} elseif (is_array ( $wheresqlarr )) {
			foreach ( $wheresqlarr as $key => $value ) {
				$where .= $comma . '`' . $key . '`' . '=\'' . $value . '\'';
				$comma = ' AND ';
			}
		} else {
			$where = $wheresqlarr;
		}
		$sql="UPDATE {$this->table($tablename)} SET $setsql WHERE $where";
		$this->execute($sql);
		
		return $this->affected_row();
	
	}
	
	public function delete($sql){
		
		$this->execute($sql);
		return $this->affected_row();
	}
	/**
	 *
	 * @param string SQL字符串
	 *       
	 * @return array 关联数组
	 */
	public function find($sql) {
		$queryrs = $this->query ( $sql );
		
		if (is_resource ( $queryrs )) {
			
			return $this->fetch_assoc ($queryrs);
		}
	
	}
	
	
	public  function affected_row(){
		return $this->affected_row;
	}
	
	
	
	public function insert_row(){
		return $this->insert_row;
	}
	
	
	public abstract function selectDb($database_name);
	protected  abstract function setUTF();
	public abstract function free();
	/**
	 * @param string $sqlstr
	 */
	public abstract function query($sqlstr);
	/**
	 * 执行S	QL返回结构集
	 * @param string $sql 
	 * @return resource 
	 */
	public abstract  function  execute($sql);
	/**
	 * 返回结构集关联数组
	 * @param resouce $query_resource
	 */
	public abstract function fetch_assoc($query_resource) ;

	/**
	 * 结果集数目
	 * @param resource $query_resource
	 */
	public abstract function rs_num_row($query_resource);
	
	
	/**
	 * 返回结果集字段数目
	 * @param resource $query_resource
	 */
	public abstract function rs_num_field($query_resource);
	
	/**
	 * 返回结果集字段数目
	 * @param resource $query_resource
	 */
	public abstract function getAffectedRows();
	/**
	 * 返回结果集字段数目
	 * @param resource $query_resource
	 */
	public abstract function insert_id();
	
}

?>