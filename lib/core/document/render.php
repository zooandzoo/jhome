<?php


class DocumentRender{
	protected  $_doc=null;
	protected $_mime='text/html';
	
	public function __construct(&$doc){
		
		$this->_doc=&$doc;
	}
	public function render(){
		
		
	}
	
}