<?php

class Request {
	
	/**
	 *
	 * @param $name string  获取变量的值;
	 *       	
	 * @param $default string  变量默认值;
	 *       	
	 * @param $method string  请求方法
	 *       	
	 * @param $type string 如WordInt,blooen 过滤形式
	 *       	 
	 */
	public static function getVar($name, $default=null, $method = 'default', $type='string') {
		$var='';
		switch ($method) {
			
			case 'GET' :
				$input = &$_GET;
				break;
			case 'POST' :
				$input = &$_POST;
				break;
			case 'FILES' :
				$input = &$_FILES;
				break;
			case 'COOKIE' :
				$input = &$_COOKIE;
				break;
			case 'ENV' :
				$input = &$_ENV;
				break;
			case 'SERVER' :
				$input = &$_SERVER;
				break;
			default :
				$input = &$_REQUEST;
				$method = 'REQUEST';
				break;
		
		}
		
		if (isset ( $input [$name] )) {
			
			$var =self::clean ( $input [$name], $type );
		}
		if(empty($var)&&$default!=null){
			
			$var=self::clean($default, $type);
			
		}
		
		return $var;
	
	}
	public static function clean($name, $type) {
		$inputobj = FilterInput::getInstance ();
		$after_filter = $inputobj->clean ( $name, $type );
		return $after_filter;
	}
	public static function setVar($name,$value=null,$method='method',$overwrite=true) {
		
		if(!$overwrite&&array_key_exists($name, $_REQUEST)){
			
			return $_REQUEST[$name];
		}
		
		$method=strtoupper($method);
		if($method==='METHOD'){
			
			$method=strtoupper($_SERVER['REQUEST_METHOD']);
		}
		$previous=array_key_exists($name,$_REQUEST)?$_REQUEST[$name]:null;
		switch($method){
			case 'GET':
				$_GET[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'POST':
				$_POST[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'COOKIE':
				$_COOKIE[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'FILES':
				$_FILES[$name] = $value;
				break;
			case 'ENV':
				$_ENV['name'] = $value;
				break;
			case 'SERVER':
				$_SERVER['name'] = $value;
				break;
			
		}
		
		return $previous;
		
	}
	
	/**
	 * @param string $name 变量值
	 * @param string $default 变量默认值
	 * @param string $method 请求方法
	 */
	public static function getFloat($name, $default = 0.0, $method) {
		return  self::getVar ( $name, $default, $method, 'float' );
	}
	
	public static function getBool($name, $default = FALSE, $method = 'default') {
		return self::getVar ( $name, $default, $method, 'bool' );
	}
	
	public static function getInt($name, $default = 0, $method = 'default') {
		return self::getVar ( $name, $default, $method, 'int' );
	}
	
	public static function getWord($name, $default = '', $method = 'default') {
		return self::getVar ( $name, $default, $method, 'word' );
	}
	
	public static function getWordInt($name, $default = '', $method = 'default') {
		return self::getVar ( $name, $default, $method, 'wordint' );
	}
	
	
	public static function checkToken($method='post'){
		$token=Session::getFormToken();
		if(!self::getVar($token,'',$method,'alnum')){
			
			return false;
		}else{
			
			return true;
		}
	}
}
?>