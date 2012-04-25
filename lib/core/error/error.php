<?php

class Error{
	
	protected static $function=array(E_NOTICE=>'appErrorMessage',E_WARNING=>'appErrorMessage',E_ERROR=>'appCustomPage');
	
	public static function appWarning($code,$message,$level){
		
		
	   Error::dealError(E_WARNING,$message,$code);
	}
	
	public static function appNotice($code,$message,$level){
	
	
		Error::dealError(E_NOTICE,$message,$code);
	}
	
	public static  function appError($code,$message,$level){
		
		
		Error::dealError(E_ERROR,$message,$code);
	}
	
	public static function dealError($level,$message,$cod){
		
		
		$exception= new myException($message, $code, $level);
		Error::throwError($message, $code, $level);
	}
	
	public static function throwError(&$Exception){
		$message=$Exception->get('message');
		$level=$Exception->get('level');
		
		call_user_func(self::$function[$level],$Exception);
	}
	
	public static function appErrorMessage(&$Exception){
		$app=Factory::getApplication();
		$message=$Exception->get('message');
		$type = ($Exception->get('level') == E_NOTICE) ? 'notice' : 'error';
		$app->messageQueue($message, $type);
		
	}
	
	
	public static function appCustomPage(&$Exception){
		
		
	}
}



?>