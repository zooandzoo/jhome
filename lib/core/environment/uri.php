<?php
class URI extends BaseObject {
	
	protected $uri = null;
	protected $scheme = null;
	protected $host = null;
	protected $port = null;
	protected $query = null;
	protected $path = null;
	protected $fragment = null;
	protected $query_var = array ();
	protected $query_var_string='';
	protected static $base = array ();
	protected static $root = array ();
	protected static $common = array ();
	protected static $instances = array ();
	
	private function __construct($uri) {
		
		$this->parseUri ( $uri );
	}
	
	/**
	 * @param STRING $uri 获取指定URI类型，$uri可以是指定的一个URL，默认解析当前脚本执行的URI
	 * @return multitype:
	 */
	public static function getInstance($uri = 'SERVER') {
		if (empty ( self::$instances [$uri] )) {
			if($uri=='SERVER')
			{
				if (! empty ( $_SERVER ['REQUEST_URI'] )) {		
					$theuri = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
				} else {
					if (isset ( $_SERVER ['QUERY_STRING'] ) && ! empty ( $_SERVER ['QUERY_STRING'] )) {
						$theuri = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['SCRIPT_NAME'] . $_SERVER ['QUERY_STRING'];
					}
				}
			}else{		
				$theuri=$uri;
			}
			self::$instances [$uri] = new URI ( $theuri );
			return self::$instances [$uri];
		}
		return self::$instances [$uri];
	}
	
	public function parseUri($uri) {
		
		if ($uri != null) {
			
			$this->uri = $uri;
			$part = parse_url ( $uri );
			$this->scheme = isset ( $part ['scheme'] ) ? $part ['scheme'] : null;
			$this->host = isset ( $part ['host'] ) ? $part ['host'] : null;
			$this->port = isset ( $part ['port'] ) ? $part ['port'] : null;
			$this->query = isset ( $part ['query'] ) ? $part ['query'] : null;
			$this->path = isset ( $part ['path'] ) ? $part ['path'] : null;
			$this->fragment = isset ( $part ['fragment'] ) ? $part ['fragment'] : null;
			if (isset ( $part ['query'] )) {
				
				parse_str ( $part ['query'], $this->query_var );
			}
		
		}
	}
	
	public static  function base() {
		
		if (empty ( self::$base )) {
			//初始化当前脚本URI对象
			$uri = self::getInstance ();
			
			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
			{
				$script_name = $_SERVER['PHP_SELF'];
			}
			else
			{
				$script_name = $_SERVER['SCRIPT_NAME'];
			}	
			//pathonly=false http://127.0.0.1/my/home
			//pathonly=true  APP_PATH
			self::$base['prefix']=$uri->toString(array('scheme','host','port'));
			
			$app=str_replace(realpath(WEB_ROOT), '', realpath(APP_PATH));
			$app_path=str_replace('\\', '/', $app);//相对于网站根目录路径
			//root部分
			$temp=realpath(dirname($script_name).'/'.WEB_ROOT);
			$rootname=isset($temp)?preg_replace('/.*[\/\\\\]/', '', $temp):'';
			$suffix=str_replace('\\', '/', '/'.$rootname.$app); //  /my/home
			$fulluri=self::$base['prefix'].$suffix;
			self::$base['full_uri']=$fulluri;
			self::$base['pathonly']=APP_PATH;

		}
	
		return self::$base;
		
	}
	
	public static function common(){
		
		if(empty(self::$common)){
			$uri = self::getInstance ();
			self::$common['prefix']=$uri->toString(array('scheme','host','port'));
			
			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
			{
				$script_name = $_SERVER['PHP_SELF'];
			}
			else
			{
				$script_name = $_SERVER['SCRIPT_NAME'];
			}
			$temp=realpath(dirname($script_name).'/'.WEB_ROOT);
			$rootname=isset($temp)?preg_replace('/.*[\/\\\\]/', '', $temp):'';
			$fulluri=self::$common['prefix'].'/'.$rootname.'/common';
			self::$common['full_uri']=$fulluri;
			self::$common['pathonly']=WEB_ROOT.'common';
		}
		

		return self::$common;
		
	
		
	}
	public static function root(){
	
		if(empty(self::$root)){
			$uri = self::getInstance ();
			
			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
			{
				$script_name = $_SERVER['PHP_SELF'];
			}
			else
			{
				$script_name = $_SERVER['SCRIPT_NAME'];
			}
			$temp=realpath(dirname($script_name).'/'.WEB_ROOT);
			$rootname=isset($temp)?preg_replace('/.*[\/\\\\]/', '', $temp):'';
			self::$root['prefix']=$uri->toString(array('scheme','host','port'));
			self::$root['full_uri']=self::$root['prefix'].'/'.$rootname;
			self::$root['pathonly']=WEB_ROOT;
			
		}
		
		

		return self::$root;
	}
	
	public function __toString(){
		
		return $this->toString();
		
	}
	
	/* 
	 * 把当前URI对象转换成字符串
	 * params array 需要连接的属性
	 */
	public function toString($parts=array('scheme','host','port','path','query','fragment')){
		
		$query=$this->getQuery();
		$uri='';
		$uri .=in_array('scheme', $parts)?(!empty($this->scheme))?$this->scheme.'://':'':'';
		$uri .=in_array('host', $parts)?$this->host:'';
		$uri .=in_array('port', $parts)?(!empty($this->port)?':':'').$this->port:'';
		$uri .=in_array('query', $parts)?(!empty($query)?'?'.$query:''):'';
		$uri .=in_array('fragment', $parts)?(!empty($this->fragment) ? '#' . $this->fragment : '') : '';
		return $uri;
	}
	
	/**获取查询字符串
	 * 
	 */
	public function getQuery($toArray=false){
		
		if($toArray){
			
			return $this->query_var;
		}
		if(is_null($this->query_var)){
			
			$this->query_var_string=self::bulidQuery($this->query_var);
		}
		
		return $this->query_var_string;
		
	}
	
	/**
	 * 查询变量数组组合成字符串
	 * @param array $var_array 
	 * @return string 
	 */
	public static  function bulidQuery($var_array){
		if(!is_array($var_array)||count($var_array)==0){
			
			return  false;
		}
		return urlencode(http_build_query($var_array,'','&'));
		
	}
}

?>