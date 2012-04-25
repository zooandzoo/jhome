<?php

function smarty_block_jscode($params,$content, $template) {
	$document = Factory::getDocument ();
	if (! empty ( $content)) {
		$document->addScriptCode($content);
	}

}
