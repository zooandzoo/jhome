<?php
/**
 * 
 * 用户验证模块
 * 通过插进实现
 * @author Administrator
 *	
 */
class Auth{
	
	protected   $authplugs=array();
	public function __construct(){
		
		$this->authplugs=Plugin::importPlugin('auth');
	}
	
	/**
	 * 验证
	 * @param array $input	输入验证
	 * @return array 响应数组
	 */
	public function auth($input){
		
		foreach($this->authplugs as $key => $value){
				$response=$value->onUserauth($input);
				if($response['status']==='success'){
					break;
				}
		}
		
		
		return $response;
	}
	
	
}