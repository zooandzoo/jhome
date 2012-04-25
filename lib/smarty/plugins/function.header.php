<?php


function smarty_function_header($params, $template)
{
			$document=Factory::getDocument();
			$headers=$document->fetchHeader();
			
			return $headers;
}

?>