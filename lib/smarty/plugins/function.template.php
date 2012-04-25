<?php

function smarty_function_template($params, $template) {
	$app = Factory::getApplication ();
	$tempalte_params_obj = $app->getTemplate ();
	if ($params ['type'] != 'name' && preg_match ( '/_([a-zA-Z])/', $params ['type'], $matches )) {
		
		if (! empty ( $matches [1] ) && !empty($tempalte_params_obj->param [$matches [1]])) {
			
			$return_string = $tempalte_params_obj->param [$matches [1]];
		} else {
			
			$return_string = '模板标签属性解析错误';
		}
	} else {
		$return_string = $tempalte_params_obj->tplname;
	}
	return $return_string;

}
