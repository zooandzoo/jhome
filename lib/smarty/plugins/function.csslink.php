<?php

function smarty_function_csslink($params, $template)
{
	$document=Factory::getDocument();
	if(!empty($params['src'])){
			$document->addStyles($params['src']);
	}


}
