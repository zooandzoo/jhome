<?php

function smarty_function_jslink($params, $template)
{
	$document=Factory::getDocument();
	$params['type']=!empty($params['type'])?$params['type']:'text/javascript';
	$params['defer']=!empty($params['defer'])?$params['defer']:false;
	$params['async']=!empty($params['async'])?$params['async']:false;
	$document->addScripts($params['src'],$params['type'],$params['defer'],$params['async']);

}
