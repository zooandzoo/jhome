<?php
class Helper{
	
	public static function checkRealName($com_name){
		$rs=true;
		$app=Factory::getApplication();
		$config_array=Factory::getWebConfig('config');
		$userinfo_array=Factory::getUser(null,'userinfo',true);
		if($config_array['realname']&&$userinfo_array['namestatus']&&empty($config_array['name_allow'.$com_name])){
			$app->enqueueMessage('您没有进行实名认证','error');
		}
		
		return $rs;
		
	}
	
	/**
	 * 检测新用户
	 */
	public static function checkNewUser(){
		$result=true;
		$app=Factory::getApplication();
		$timenow=$app->requestTime;
		$userinfo=Factory::getUser(null,'userinfo',true);
		$webconfig=Factory::getWebConfig('config');
		if(Access::userActionAccess('spamignore')){
			return $result;
		}
		
		if($webconfig['newusertime']&&$timenow-$userinfo['dateline']<$webconfig['newusertime']){
			$app->enqueueMessage('您正处在见习时间，暂时不能发帖','error');
			return false;
		}
		
		if($webconfig['need_avatar'] && $userinfo['dateline']<$webconfig['need_avatar']){
			$app->enqueueMessage('您还没有上传头像','error');
			return false;
		}
		
		if($webconfig['need_friendnum'] && $userinfo['friendnum']<$webconfig['need_friendnum']){
			$app->enqueueMessage('您的好友数量没有达到要求','error');
			return false;
		}
		if($webconfig['need_email'] && empty($userinfo['emailcheck'])){
			$app->enqueueMessage('您的的邮箱没有经过验证','error');
			return false;
		}
		
		return $result;
	}
	
	
	public static function checkInterval($com_name){
		$app=Factory::getApplication();
		$timenow=$app->requestTime;
		$userinfo=Factory::getUser(null,'userinfo',true);
		$webconfig=Factory::getWebConfig('config');
		
		$intervalname = $com_name.'interval';
		$lastname = 'last'.$com_name;
		
		if($interval = Access::userActionAccess($intervalname)){
			$lasttime=isset($userinfo[$lastname])?$userinfo[$lastname]:0;
			$waittime=$interval-($timenow-$lasttime);
		}
		
		return $waittime;
	}
	
}