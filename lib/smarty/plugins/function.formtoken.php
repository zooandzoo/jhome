<?php

function smarty_function_formtoken($params, $template)
{
	$input_html='';
	$hash=Session::getFormToken();
	$input_html='<input type="hidden" value="1" name="'.$hash.'">';
	return $input_html;

}
