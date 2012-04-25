<?php



class ClassLoader {
	
	public static $_import = array ();
	public static $classes=array();
	
	public static function regautoload() {
		
		spl_autoload_register(array('ClassLoader','_autoload'));
		spl_autoload_register(array('ClassLoader','_load'));
		
	}
	
	public static function _load($classname){
		$classname=strtolower($classname);
		if(class_exists($classname)){
			return true;
		}
		if(isset(self::$classes[$classname])){
			
			include_once self::$classes[$classname];
		}
		
		return false;
	}
	
	/**
	 * 
	 * 注册函数但不导入
	 * @param string $class
	 * @param string $fullpath
	 */
	public static function register($class,$fullpath){
		
		$classname=strtolower($class);
		if(!empty($class)&&is_file($fullpath)){
			
			if(empty(self::$classes[$classname])){
				
				self::$classes[$classname]=$fullpath;
			}
			
		}
		
	}
	public static  function _autoload($class_name) {
		
		if (class_exists ( $class_name )) {
			
			return true;
		}
		
		$array=preg_split ( '/(?<=[a-z])(?=[A-Z])/x', $class_name);
		
		if (count ( $array ) == 1) {
			$name = array ($array [0], $array [0] );
		} else {
			$name = $array;
		}
		
		$path = FRAME_CORE . '/' . implode ( '/', array_map ( 'strtolower',$name)) . '.php';
		if (file_exists ( $path )) {
			
			include_once $path;
		}
	}
	
	public static function import($packages, $base = null) {
		
		if (! isset ( self::$_import [$packages] )) {
			
			$folderlevel = explode ( $packages, '.' );
			
			$base = isset ( $base ) ? $base : dirname ( __FILE__ );
			
			$level = str_replace ( '.', '/', $packages );
			
			$file = $base . '/' . $level . '.php';
			
			if (file_exists ( $file )) {
				
				self::$_import [$packages] = $file;
				include_once $file;
			
			} else {
				
				exit ( '包含文件不存在' );
			}
		
		}
		
		return self::$_import[$packages];
	}

}

function kimport($packages) {
	
	classLoader::import ( $packages );

}

?>