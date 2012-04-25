<?php
class JSubject extends BaseObject {
	
	protected $_observer = array ();
	
	protected $_state = null;
	
	protected $_methods = array ();
	
	protected static $instance = null;
	
	public static function getInstance() {
		
		if (self::$instance === null) {
			
			self::$instance = new JSubject ();
		}
		
		return self::$instance;
	}
	
	public function attach($plugin) {
		
		if($plugin instanceof Observer)
		{
			$class_name=get_class($plugin);
			
			foreach($this->_observer as $check){
				if($check instanceof $class_name){
					
					return ;
				}
				
			}
			
			$this->_observer[]=$plugin;
			$methods=array_diff(get_class_methods($plugin), get_class_methods('Plugin')) ;
			
			$key=key($this->_observer);
			
			foreach ($methods as $method){
				
				$method=strtolower($method);
				
				if(!isset($this->_methods[$method])){
					$this->_methods[$method]=array();
					
				}
				$this->_methods[$method][]=$key;
				
			}
			
		}
	
	}
	
	public function trigger($method,$args=array()){
		
		$value=array();
		$method=strtolower($method);
		if(!isset($this->_methods[$method])||empty($this->_methods[$method])){
			
			return $value;
		}
		foreach($this->_methods[$method] as $v){
			
			if(!isset($this->_observer[$v])){
				continue;
			}
			if(is_object($this->_observer[$key])){
				
				$args['method']=$method;
				$value=$this->_observer[$key]->update($args);
			}
			
			
		}
		return $value;
	}
	
	public function detach($plugins_name) {
		
		$return=false;
		$key=array_search($plugins_name, $this->_observer);
		
		if($key !==false){
			unset($this->_observer[$key]);
			$return=true;
			foreach($this->_methods as &$method){
				
				$k=array_search($key, $method);
				if($k!==false){
					
					unset($method[$k]);
				}
			}
		}
		return $return;
	}
	


}