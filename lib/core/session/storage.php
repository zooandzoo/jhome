<?php

class SessionStorage {
	protected static $instance;
	
	
	public function __construct() {
		$this->register();
	}
	
	public function register(){
		session_set_save_handler(
				array($this,'open'),
				array($this,'close'),
				array($this,'read'),
				array($this,'write'),
				array($this,'destroy'),
				array($this,'gc')
		);
	}
	public function open($save_path,$session_name){
		
		return true;
	}
	public function close(){
		return true;
		
	}
	public function read($id){
		
		return ;
	}
	public function write($id,$session_data){
		return true;
		
	}
	
	public function destroy($id){
		
		return true;
	}
	
	public function gc($lifetime=null){
		
		return true;
	}
	public static function getInstance($handle_name, $options) {
		
		if (empty ( self::$instance [$handle_name] )) {
			$class = 'SessionStorage' . ucfirst ( $handle_name );
			
			if (! class_exists ( $class )) {
				$path = dirname ( __FILE__ ) . '/storage/' . $handle_name . '.php';
				if (file_exists ( $path )) {
					require_once $path;
				}else{
					exit('session存储引擎加载失败');
				}
			}
				self::$instance[$handle_name]=new $class($options);
		}
				return self::$instance[$handle_name];
	}
	
	
	

}