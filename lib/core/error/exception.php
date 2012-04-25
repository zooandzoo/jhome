<?php
class myException extends Exception{
	
	
	protected $level=null;
	protected $code=null;
	protected $message=null;
	
	public function __construct($message,$code,$level){
		
		$this->level=$level;
		$this->code=$code;
		$this->message=$message;
		parent::__construct($message,$code);
		
	}
	
	
	
}