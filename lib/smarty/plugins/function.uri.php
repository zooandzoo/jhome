<?php

function smarty_function_uri($params, $template) {
	$params ['pathonly'] = isset ( $params ['pathonly'] ) ? $params ['pathonly'] : false;
	$uri_type = array ('base', 'common', 'root' );
	$function_name = $params ['type'];
	if (! in_array ( $function_name, $uri_type )) {
		 throw new SmartyException("URI标签类型不支持");
	}
	$url_array = URI::$function_name ();
	if ($params ['pathonly']) {
		
		return $url_array ['pathonly'];
	} else {
		return $url_array ['full_uri'];
	}

}
