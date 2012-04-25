<?php
class Path{
	
	/**
	 * 在路径数组查找文件是否存在
	 * @param array $path_array 
	 * @param string $file
	 */
	public static function find($path_array,$filename){
		settype($path_array, 'array');
		foreach($path_array as $path){		
			$fullname=$path.'/'.$filename;			
			if(strpos($path, '://')===false){
				$path=realpath($path);
				$fullname=realpath($fullname);			
			}	
			if(file_exists($fullname)&&substr($fullname, 0,strlen($path))==$path){
				return $fullname;		
			}		
		}	
		return FALSE;	
	}
	
	public static function clean($path,$ds=DIRECTORY_SEPARATOR){
		
		$path=trim($path);
		if(empty($path)){
			$path=ROOT;
			
		}else{
			$path=preg_replace('/[/\\\\]+/', $ds, $path);
			
		}
		return $path;
	}
	
	
}