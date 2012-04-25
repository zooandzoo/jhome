<?php
class  Access extends BaseObject{
	
	
	public static  function userActionAccess($action,$userid=0,$forceallow=FALSE){
		
		if($forceallow){
			return true;
		}
		$usergroup=Factory::getUser(null,'usergroup');
		if(isset($usergroup[$action])){
			return $usergroup[$action];
		}
		
	}
	
	
}