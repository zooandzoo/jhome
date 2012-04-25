<?php

class ArrayHelper {
	
	/**
	 *
	 *
	 * 把数组转换成字符串
	 * 
	 * @param $array array       	
	 * @param $inner_glue string       	
	 * @param $outer_glue string       	
	 * @param $keepOuterKey boolen       	
	 * @return string
	 */
	public static function toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false) {
		
		$output = array ();
		if (is_array ( $array )) {
			foreach ( $array as $key => $value ) {
				if (is_array ( $value )) {
					if ($keepOuterKey) {
						
						$output [] = $key;
					}
					ArrayHelper::toString ( $value, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey );
				} else {
					
					$output [] = $key . $inner_glue . '"' . $value . '"';
				}
			
			}
		
		}
		
		return implode ( $outer_glue, $output );
	
	}
	
	/**
	 * 数组缓存生成
	 * @param string $name
	 * @param string $path	/config
	 * @param array $value
	 * @return boolean|number|Ambigous <>
	 */
	public static function arrayDataFile($name,$path='', $value = array()) {
		$datapath = !empty($path)?ROOT.$path:ROOT . '/data';
		$filename = $datapath .'/'. $name . '.php';
		static $_cache = array ();
		if ('' !== $value) {
			if (is_null ( $value )) {
				// 删除缓存
				return unlink ( $filename );
			} else {
				// 缓存数据
				$dir = dirname ( $filename );
				// 目录不存在则创建
				if (! is_dir ( $dir ))
					mkdir ( $dir );
				return file_put_contents ( $filename, "<?php\nreturn " . var_export ( $value, true ) . ";\n?>" );
			}
		
		}
		
		if (isset($_cache[$name]))
			return $_cache[$name];
		// 获取缓存数据
		if (is_file($filename)) {
			$value = include $filename;
			$_cache[$name] = $value;
		} else {
			$value = false;
		}
		return $value;
		
	
	}
	
	public static function toDataArray($array) {
		
		return var_export ( $array );
	
	}
	
	public static function toInteger(&$array) {
		
		if (is_array ( $array )) {
			
			foreach ( $array as $key => $value ) {
				
				if (is_array ( $value )) {
					
					self::toInteger ( $value );
				}
				
				$array [$key] = ( int ) $value;
			}
		
		}
	
	}

}