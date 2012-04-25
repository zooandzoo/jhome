<?php

function smarty_function_messages($params, $template)
{
	
	$style=isset($params['style'])?$params['style']:'default';
	$template->compile_dir = CURRENT_APP_PATH . '/data/templates_c/sys';
	$template->assign('style',$style);
	
	$session=Factory::getSession();
	$messages=$session->get('application.queue');
	$html_contant='';
	if(isset($messages)){	
			foreach($messages as $key=>$value){	
					if($value['type']=='message'){	
						
						$template->assign('message_string',$value['message']);
						$html_contant .=$template->fetch(CURRENT_APP_PATH.'/templates/sys/message/success.html').'<br>';
					
					}
					if($value['type']=='error'){
						$template->assign('message_string',$value['message']);
						$html_contant .=$template->fetch(CURRENT_APP_PATH.'/templates/sys/message/error.html').'<br>';
					}				
			}
		$session->set('application.queue',null);
	}
	
	return $html_contant;

}
