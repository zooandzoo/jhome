<?php
	class CacheController{
		
		public $cache=null;
		public $option=array();
		
		
		
		public function __construct($option){
			$this->cache=new Cache($option);
			$this->option =& $this->cache->option;

		}
		public static function getInstance($type,$option){
			
			$type = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $type));
			$classname='CacheController'.ucfirst($type);
			if(!class_exists($classname)){
				$path=dirname(__FILE__).'/controller'.$type.'.php';
				if(file_exists($path)){
					require_once $path;
				}else{
					exit('存储控制器不存在');
				}
				
			}
			return new $classname($option);
			
		}
		
		
	}