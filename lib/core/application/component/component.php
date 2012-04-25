<?php

/**
 * @author Administrator
 * 
 *
 */
class Component extends BaseObject{
	
	public static $component=array();
	
	/**
	 * @param string $string 
	 * @return bool 
	 */
	public static function isEnable($string){
		
		$rs=self::getComponents($string);
		
		return $rs->enabled;
		
	}
	
	/**
	 * @param string $component  'com_banners'
	 */
	public static function displayComponents($component){
	
		$application=Factory::getApplication();
		define('CURRENT_COMPONENT_PATH', CURRENT_APP_PATH.'/components/'.$component);
		define('SITE_COMPONENT_PATH', ROOT.'/home/components/'.$component);
		$file=substr($component,4);
		$path=CURRENT_COMPONENT_PATH.'/'.$file.'.php';
		if(!file_exists($path)){
			
			exit('组件入口文件不存在');
		}
	
		$componenthtml=self::executeComponents($path);
		
		return $componenthtml;
	}
	
	/**
	 * 
	 * 
	 * @param string $string component 
	 * @return string component
	 */
	public static function executeComponents($component_path){
		
		ob_start();
		require_once $component_path;
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
		
		
		
	}
	public static function getComponents($string){
		if(!isset(self::$modoule[$string])){
			
			if(self::loadComponents($string)){
				
				$component=self::$component[$string];
			}else{
				
				$component=new stdClass();
				$component->enabled=false;
				self::$component[$string]=$component;
				
			}
		}
		
		return self::$modoule[$string];
	}
	
	
	
	
	public static function loadComponents($string){
		
		$componentobj=new stdClass();
		$componentobj->id=1;
		$componentobj->action='login';
		$componentobj->name='lgoin';
		$componentobj->params='';
		$componentobj->enabled=1;
		
		self::$component[$string]=$componentobj;
		
		return self::$modoule[$string];
		
	}
	
}
?>