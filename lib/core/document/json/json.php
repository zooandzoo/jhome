<?php 

class DocumentJSON extends Document{
	
	
	public function __construct($option=array()){
		
		$this->_mime='application/json';
		$this->_type='json';
	}
	
	public function render(){
		
		parent::render();
		return $this->getBuffer();
	}
	
	
	
}

