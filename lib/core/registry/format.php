<?php
abstract class RegistryFormat{
	
	
	public static $instance=array();
	
	public static function getInstance($type){
		
		if(empty(self::$instance[$type])){
			$classname='RegistryFormat'.strtoupper($type);
			if(!class_exists($classname)){
				$path=dirname(__FILE__).'/format/'.$type.'.php';
				if(is_file($path)){
					
					require_once $path;
				}else{
					
					exit('格式化数据文件不存在');
				}
				
			}
			self::$instance[$type]=new $classname;
			
		}
		
		return self::$instance[$type];
		
	}
	
	abstract public function stringToObject($data,$options = null);
	abstract public function objecToString($data,$options = null);
}