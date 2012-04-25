<?php

function smarty_function_title($params, $template)
{
	$document=Factory::getDocument();
	$string=$document->getTitle();
	$return_str="<title>$string</title>";

	return $return_str;
}
