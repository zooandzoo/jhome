<?php

class Response {
	
	public static $header = array ();
	public static $body = array ();
	public static $cache = FALSE;
	
	public static function setHeader($name, $value, $replace=false) {
		$name = ( string ) $name;
		$value = ( string ) $value;
		if ($replace) {
			foreach ( self::$header as $key => $header ) {
				if ($name == $header ['name']) {
					unset ( self::$header [$key] );
				}
			}
		}
		self::$header [] = array ('name' => $name, 'value' => $value );
	}
	
	public static function sendHeaders() {
		
		if (! headers_sent ()) {
			
			foreach ( self::$header as $value ) {
				
				if ('status' == strtolower ( $value ['name'] )) {
					header ( ucfirst ( strtolower ( $value ['name'] ) ) . ': ' . $value ['value'], null, ( int ) $value ['value'] );
				} else {
					header ( $value ['name'] . ': ' . $value ['value'], false );
				}
			
			}
		
		}
	
	}
	
	public static function setBody($body_content) {
		
		self::$body = array ($body_content );
	}
	public static function getBody($return_array = FALSE) {
		
		if ($return_array) {
			return self::$body;
		}
		
		ob_start ();
		foreach ( self::$body as $content ) {
			
			echo $content;
		}
		return ob_get_clean ();
	}
	
	public static function toString($compress = FALSE) {
		$data = self::getBody ();
		// 如果服务器已经启动了压缩那么跳过，让服务器处理压缩
		if ($compress && ! ini_get ( 'zlib.output_compression' ) && ! ini_get ( 'output_handler' ) != 'ob_gzhandler') {
			$data = self::compress ( $data );
		}
		if (self::allowCache () === false) {
			self::setHeader ( 'Cache-Control', 'no-cache', false );
			self::setHeader ( 'Pragma', 'no-cache' );
		
		}
		self::sendHeaders();
		return $data;
		
	
	}
	
	public static function compress($data) {
		$encdoing= self::clientEncoding();
		if(!$encdoing){
			
			return $data;
		}
		if(!extension_loaded('zlib')|| ini_get('zlib.output_compression')){
			
			return $data;
		}
		if(headers_sent()){
			return $data;
			
		}
		if(connection_status()!=0){
			return $data;
		}
		$level=4;
		$gzipdata=gzencode($data,$level);
		self::sendHeaders('Content-Encoding',$encdoing);
		return $gzipdata;
	
	}
	
	public static function clientEncoding(){
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING'])){
			
			return false;
		}
		$encoding=false;
		if(false != strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')){
			$encoding='gzip';
			
		}
		if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip'))
		{
			$encoding = 'x-gzip';
		}
		
		return $encoding;
		
	}
	public static function allowCache($cache = null) {
		
		if (! is_null ( $cache )) {
			
			self::$cache = ( bool ) $cache;
		}
		
		return self::$cache;
	
	}

}

