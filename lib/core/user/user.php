<?php
class User extends BaseObject{
	protected  $_userinfo=array();
	protected $_usergroup=array();
	protected $guest=1;
	public static $instance=array();
	
	
	public function __construct($id){
		
		//当ID为不为0的数字时
		if(isset($id)&&is_numeric($id)){
			
			$dbo=Factory::getDbo();
			$one=$dbo->find("SELECT s.*,sf.* FROM {$dbo->table('space')} s LEFT JOIN {$dbo->table('spacefield')} sf ON sf.uid= s.uid WHERE s.uid=$id");
			if($one){
				
				$this->_userinfo=$one;
				$this->_usergroup=$this->getUserGroupById($one['uid']);
				$this->guest=0;
			}
			
		}
		
	}
	public static function getInstance($identifier=0){
		
		if(!is_numeric($identifier)&&isset($identifier)){
			if(!self::getUserByName($identifier)){
				return false;
			}
		}else{
			$id=$identifier;
		}
		
		if(empty(self::$instance[$id])){
			$user=new User($id);
			self::$instance[$id]=$user;
		}
		
		return self::$instance[$id];
	}
	
	public static function getUserByName($username){
		$dbo=Factory::getDbo();
		$rs=$dbo->find("SELECT * FROM ".$dbo->table('member')." WHERE username = $username");
		if(!empty($rs)){
			
			$this->guest=0;
		}
		return $rs;
	}
	
	public function getUserGroup(){
		
		if(!empty($this->_usergroup)){
			return $this->_usergroup;
		}
		return false;
	}
	public function getUserInfo(){
		if(!empty($this->_userinfo)){
			return $this->_userinfo;
		}
		return false;
		
	}
	protected  function getUserGroupById($uid){
		if(!empty($this->_userinfo)&&empty($uid)){
			$id=$this->_userinfo['groupid'];
		}else{
			$dbo=Factory::getDbo();
			$one=$dbo->find("SELECT s.*,sf.* FROM {$dbo->table('space')} s LEFT JOIN {$dbo->table('spacefield')} sf ON sf.uid= s.uid WHERE s.uid=$uid");
			$id=$one['groupid'];
		}
		if(!empty($id)){
			$data=ROOT.'/data/';
			$fullpath=$data.'data_group_'.$id.'.php';
			if(file_exists($fullpath)){
				
					return 		include $fullpath;
			}else{
				
				return array();
			}
			
		}
		return false;
	}
	public function isGuest(){
		if($this->guest){
			
			return true;
		}else{
			
			return false;
		}
		
	}
}