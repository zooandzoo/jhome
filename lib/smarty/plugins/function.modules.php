<?php

/**
 * 模块解析标签，用来调用单个模块，或者在同一位置的多个模块
 * 
 * {modules name="login" position="position-1" style=""}
 * @param array $params
 * @param unknown_type $template
 */
function smarty_function_modules($params, $template) {
	$buffer = '';
	
	if (! empty ( $params ['name'] )) {
		
		$attr_array = Module::getModule ( $params ['name'] );
		$buffer = Module::renderModule ( $attr_array );
		
		
	} elseif (! empty ( $params ['position'] )) {
		
		$modules_list = Module::getModules ( $params ['position'] );
		
		foreach ( $modules_list as $key => $value ) {
			
			$buffer .= Module::renderModule ( $value );
		}
	
	}
	
	return $buffer;
}		
