<?php

class SessionStorageMemcache extends SessionStorage{
	
	private $_resource=null;
	private $_servers=array();
	private $_compress=null;
	private $_persistent = false;
	
	public function __construct($options=array()){
		
		if(!$this->test()){
			
			exit('Memcache未开启');
		}
		parent::__construct();
		$config_obj=Factory::getConfig();
		$memcacheconfig=unserialize($config_obj->session_memcache_settings);
		if(empty($memcacheconfig)){
			
			$memcacheconfig=array();
		}
		$this->_compress=isset($memcacheconfig['compression'])?$memcacheconfig['compression']:0;
		$this->_persistent=isset($memcacheconfig['persistent'])?$memcacheconfig['persistent']:0;
		$this->_servers=isset($memcacheconfig['servers'])?$memcacheconfig['servers']:array();
	}
	public function open($save_path, $session_name)
	{
		
		$this->_resource=new Memcache;
		for($i=0 ; $i<count($this->_servers ); $i++){
			
			$server=$this->_servers[$i];
			$this->_resource->addServer($server['host'],$server['port'],$this->_persistent);
		}
		
	}
	
	public function close(){
		
		$this->_resource->close();
	}
	
	public function read($id){
		
		
		return $this->_resource->get($id);
		
	}
	public function write($id, $session_data){
		if($this->_resource->get($id)){
			
			$this->_resource->replace($id,$session_data,$this->_compress);
		}else{
			
			$this->_resource->set($id,$session_data,$this->_compress);
		}
		return;
		
	}
	
	public function destroy($id){
		return $this->_resource->delete($id);
		
	}
	
	public function gc($lifetime){
		
		return true;
	}
	public function test(){
		
		return (extension_loaded('memcache')&&class_exists('Memcache'));
	} 
	
	
}