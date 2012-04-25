<?php

function smarty_block_csscode($params,$content, $template) {
	$document = Factory::getDocument ();
	if (! empty ($content)) {
		$document->addStyleCode($content);
	}

}
