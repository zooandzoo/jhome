<?php


	/**
	 * @author Administrator
	 *	系统缓存类
	 */
	ClassLoader::register('CacheController', FRAME_CORE.'/cache/controller.php');
	ClassLoader::register('CacheStorage', FRAME_CORE.'/cache/storage.php');
	
	class Cache{
		
		/**
		 * @var object 存储对象
		 */
		public static $handle=null;
		/**
		 * @var array 选项
		 */
		public $option=array();
		
		/**
		 * 初始化缓存
		 * @param array $option
		 */
		public function __construct($option){
			$config=Factory::getConfig('web');
			$this->option=array(
						'cacheing'=>($config->caching=1)?true:false,	//是否启用缓存
						'cachebase'=>CACHEBASE,					//缓存路径
						'lifetime'=>(int)$config->cache_lifetime,
						'checktime'=>true ,					//是否检测过期时间,
						'storage'=>$config->cache_storage,	//缓存存储方式
						'defaultgroup'=>'default',			//缓存存放分组目录
						'locktime'=>15,						//锁定时间
						'locking'=>true,					//是否锁定
					);
			foreach($option as $key=>$value){
				if(isset($option[$key])&&$option[$key] !==''){
					$this->option[$key]=$option[$key];
				}
			}
			if(empty($this->option['storage'])){
				
				$this->option['caching']=false;
			}
			
			
			
			
			
		}
		
		/**
		 * 获取类型controller
		 * @param stirng $type 缓存类型
		 * @param array $option 缓存选项
		 */
		public static  function getInstance($type='callback',$option=array()){
			
			return CacheController::getInstance($type,$option);
			
		}
		
		public function get($id,$group=null){
			$group=($group)?$group:$this->option['defaultgroup'];
			$handle=$this->getStorage();
			if($this->option['caching']){
				
				return $hanler->get($id,$group,$this->option['checktime']);
			}
			return false;
		}
		
		/**
		 * 存缓存s
		 * @param string $data
		 * @param stirng $id
		 * @param string $group
		 */
		public function store($data,$id,$group){
			$group=($group)?$group:$this->option['defaultgroup'];
			$handler=$this->getStorage();
			if($this->option['caching']){
				$handler->_lifetime=$this->option['lifetime'];
				$handler->store($data,$id,$group);
			}
			
		}
		
		/**
		 * 删除缓存
		 * @param string $id
		 * @param string $group
		 */
		public function remove($id,$group){
			$group=($group)?$group:$this->option['defaultgroup'];
			$handle=$this->getStorage();
			
			return $handle->remove($id,$group);
		}
		
		/**
		 * 清除组缓存
		 * @param string $group
		 */
		public function clean($group){
			$group=($group)?$group:$this->option['defaultgroup'];
			$handle=$this->getStorage();
			return $handle->clean($group);
		}
		
		/**
		 * 清除过期缓存
		 */
		public function gc(){
			$handle=$this->getStorage();
			return $handle->gc();
			
		}
		
		public function getCaching($bool){
			
			$this->option['caching']=$bool;
			
		}
		
		public function setCaching(){
			
			return $this->option['caching'];
		}
		
		public function getLifetime(){
			
			return $this->option['lifetime'];
		}
		public function setLifeTime($loong){
			
			$this->option['lifetime']=$long;
		}
		
		
		public function getStorage(){
			$hash=md5(serialize($this->option));
			if(isset(self::$handle[$hash])){
				return self::$handle[$hash];
			}

			self::$handle[$hash]=CacheStorage::getInstance($this->option['storage'],$this->option);
			
			return self::$handle[$hash];
			
			
		}
		
		public function lock($id,$group=null,$locktime=null){
			
			$return = new stdClass();
			$return->locklooped = false;
			$group=($group)?$group:$this->option['defaultgroup'];
			$locktime=$locktime?$locktime:$this->option['locktime'];
			$handle=$this->getStorage();
			
			$handle->lock($id,$group,$locktime);
			
			
		}
		public function unlock(){}
		
		
	}