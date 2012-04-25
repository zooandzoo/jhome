<?php

class Factory {
	
	public static $application;
	public static $sesstion;
	public static $config;
	public static $datebase;
	public static $document;
	public static $template_object;
	public static $cache;
	public static $user;
	
	static function getApplication($name = '', $config = array()) {
		
		if (isset ( self::$application )) {
			
			return self::$application;
		}
		
		self::$application = Application::getInstance ( $name, $config );
		return self::$application;
	
	}
	static function getConfig($file = null) {
		
		if (! self::$config) {
			$file = isset ( $file ) ? ROOT . '/config/config_' . $file . '.php' : ROOT . '/config/config_global.php';
			if (file_exists ( $file )) {
				require_once $file;
			} else {
				exit ( 'config配置文件不存在' );
			}
			
			self::$config = self::createConfig ( $file );
		
		}
		return self::$config;
	}
	
	static function getDbo() {
		
		if (! self::$datebase) {
			
			self::$datebase = self::createDbo ();
		}
		
		return self::$datebase;
	}
	
	static function getDocument() {
		
		if (! empty ( self::$document )) {
			
			return self::$document;
		}
		return self::$document = self::createDocument ();
	}
	

	/**
	 * 如果$user_info_type为空则根据提供的ID 来获取用户，根据ID来判断是从SESSION获取还是重新建立
	 * $user_info_type不为空则返回USER对象的USERINFO数组或者USERGROUP数组
	 * @param int $id
	 * @param string $user_info_type
	 * @param boolen $force_update_user 获取$user_info_type时是否强制更新
	 * @return object (USER) array (userinfo or usergroup)
	 */
	static function getUser($id = null, $user_info_type = '',$force_update_user=false) {
		
		$instance = null;
		if(!isset($id)&&$force_update_user==true){
			$user=self::getSession ()->get ( 'user' );
			if(empty($user)){
				return User::getInstance ();
			
			}
			$info=$user->getUserInfo();

			$instance = User::getInstance ($info['uid']);
		}
		if(isset($id)||$force_update_user==true){
			
			$instance = User::getInstance ($id);
			
		}elseif(!isset($id)&&$force_update_user==false){
			
			$instance = self::getSession ()->get ( 'user' );
			
		}

		if (! empty ( $user_info_type )) {
				switch ($user_info_type){
					
					case 'userinfo': 
						$return =$instance->getUserInfo();
						break;
					case 'usergroup':
						$return=$instance->getUserGroup();
						break;
				}
		
		}
		
		
		
		return empty($user_info_type)?$instance:$return;
	}
	
	static function createDocument() {
		$type = Request::getWord ( 'format', 'html' );
		self::$document = Document::getInstance ( $type );
		
		return self::$document;
	
	}
	static function createSession($options) {
		
		$config = self::getConfig ();
		$leftime = $config->lifetime * 60;
		$storage_handle = $config->storage_handle;
		$options ['lifetime'] = $leftime;
		
		// options=array('name'=>'...','leftime'=>15)
		$seesion = Session::getInstance ( $storage_handle, $options );
		
		if ($seesion->getState () == 'expired') {
			
			$seesion->restart ();
		}
		return $seesion;
	}
	
	static function getSession($options = array()) {
		
		if (empty ( self::$sesstion )) {
			
			self::$sesstion = self::createSession ( $options );
		}
		
		return self::$sesstion;
	}
	
	static function createConfig($file) {
		
		if (file_exists ( $file )) {
			
			require_once $file;
		}
		
		$configobjeck = new Config ();
		return $configobjeck;
	}
	
	static function createDbo() {
		kimport ( 'core.database.database' );
		$configobj = self::$config;
		return $db = DataBaseObject::getInstance ( $configobj );
	
	}
	
	static function getTemplateObject() {
		
		if (empty ( self::$template_object )) {
			self::$template_object = self::createTemplateObject ();
		}
		
		return self::$template_object;
	}
	
	static function createTemplateObject() {
		$config = self::getConfig ();
		$classname = ucfirst ( strtolower ( $config->template_engine ) );
		
		$package = strtolower ( $config->template_engine ) . '.' . $classname;
		kimport ( $package );
		return $template_object = new $classname ();
	
	}
	
	static function getCache($group = '', $type = 'callback', $storage = null) {
		$hash = md5 ( $group . $type . $storage );
		if (isset ( self::$cache [$hash] )) {
			return self::$cache [$hash];
		}
		
		$option = array ('defaultgroup' => $group );
		if (isset ( $storage )) {
			$option ['storage'] = $storage;
		}
		
		$cache_obj = Cache::getInstance ( $type, $option );
		self::$cache [$hash] = $cache_obj;
		return self::$cache [$hash];
	
	}
	
	/**
	 * 获取网站配置数组
	 * 
	 * @param $config_name string       	
	 * @return array (success) false (error)
	 */
	static function getWebConfig($config_name) {
		static $config_type = array ();
		if (! empty ( $config_type [$config_name] )) {
			return $config_type [$config_name];
		}
		if (empty ( $config_name )) {
			return false;
		}
		$config_file = ROOT . '/config/config_web_' . $config_name . '.php';
		if (file_exists ( $config_file )) {
			$config_type [$config_name] = include $config_file;
			return $config_type [$config_name];
		
		} else {
			exit ( 'config 配置文件不存在' );
		}
	
	}

}

?>