<?php

class Observer{
	
	public $subject=null;
	public function __construct(&$subject,$params){
		$this->_name=$params['name'];
		$this->_type=$params['type'];
		$this->_params=unserialize($params['params']);
		$this->subject=$subject;
		$subject->attach($this);
		
	}
	public function  update($args){
		
		$method=$args['method'];
		unset($args['method']);
		if(method_exists($this, $method_name)){
			
			return call_user_func_array(array($this,$method), $args);
			
		}else{
			
			return null;
		}
	}
}