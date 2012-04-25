<?php



function smarty_function_component($params, $template) {
	
	$current_document=Factory::getDocument();
	$document_buffer=$current_document->getBuffer();
	return $document_buffer['component'];
		
}
