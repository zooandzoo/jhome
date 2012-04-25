<?php

/**
	 * @author Administrator
	 * 缓存存储驱动
	 */
class CacheStorage {
	
	protected $_locking;
	
	protected $_lifetime;
	
	public function __construct($option) {
		
		$this->_locking = isset ( $option ['locking'] ) ? $option ['locking'] : true;
		$this->_lifetime = isset ( $option ['lifetime'] ) ? $option ['lifetime'] : true;
	
	}
	
	public static function getInstance($storagename, $option) {
		if (! isset ( $storagename )) {
			
			$config = Factory::getConfig ( 'web' );
			$storagename = $config->cache_storage;
		
		}
		
		$classname = 'CacheStorage' . ucfirst ( $storagename );
		if (! class_exists ( $classname )) {
			
			$path = dirname ( __FILE__ ) . '/storage/' . $classname . '.php';
			
			if (file_exists ( $path )) {
				require_once $path;
			} else {
				exit ( '存储引擎不存在' );
			}
		
		}
		
		return new $classname ( $option );
	}
	
	public function get($id, $group, $checktime = TRUE) {
		return true;
	}
	
	public function store($id, $group, $data) {
		return true;
	}
	public function remove($id, $group) {
		return true;
	}
	public function clean($group) {
		return true;
	}
	public function lock($id,$group,$locktime){
		return true;
	}
	public function unlock($id,$group){
		return true;
	}

}