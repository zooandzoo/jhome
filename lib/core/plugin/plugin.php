<?php
kimport ( 'core.observer.subject' );
kimport ( 'core.observer.observer' );
class Plugin extends Observer {
	
	/**
	 * 插件属性
	 *
	 * @var array
	 */
	protected $_params = array ();
	
	/**
	 * 名字
	 *
	 * @var string
	 */
	protected $_name = '';
	
	/**
	 * 类型
	 *
	 * @var string
	 */
	protected $_type = '';
	
	/**
	 * 插件对象数组
	 *
	 * @var array
	 */
	public static $plugins = array ();
	
	public static  function getPlugins($type, $name = '') {
		$return = array ();
		$plugins = self::_load ();
		if (empty ( $name )) {
			foreach ( $plugins as $value ) {
				if ($value ['type'] == $type) {
					
					$return [] = $value;
				}
			
			}
		
		} else {
			foreach ( $plugins as $value ) {
				
				if ($type == $value ['type'] && $name = $value ['name']) {
					
					$return = $value;
					break;
				}
			}
		
		}
		return $return;
	
	}
	/**
	 * 检测是否开启插件
	 * @param string $type
	 * @param string $plugins_name
	 * @return boolean
	 */
	public static function isEnable($type, $plugins_name) {
		$plugins_list = self::_load ();
		$enable = false;
		foreach ( $plugins_list as $value ) {
			
			if (! empty ( $value ['$type'] ) && ! empty ( $value [$plugins_name] )) {
				$enable = true;
				return $enable;
			}
		}
		return $enable;
	
	}
	
	/**
	 * 批量引入单个引入指定的插件
	 * 
	 * @param $type string       	
	 * @param $name string       	
	 * @return array 返回导入的插件对象，如果为空导入插件对象失败
	 */
	public static function importPlugin($type, $name='') {
		$temp_array=array();
		static $loaded = array ();
		if (! isset ( $loaded [$type] )) {
			
			$plugins = self::_load ();
			for($i = 0; $i < count ( $plugins ); $i ++) {
				
				if ($type == $plugins [$i] ['type'] || $plugins [$i] ['name'] == $name) {
					$return_plug=self::_importPlugin ( $plugins [$i] );
					
					if($return_plug!==null){
						
						$temp_array[]=$return_plug;
					}else{
						$temp_array=array();
					}
				}
			
			}
			$loaded [$type]=$temp_array;
			
		}

		return $loaded [$type];
	}
	
	/**
	 * 从数据库或者缓存读取插件信息
	 * @return array
	 */
	protected static  function _load() {
		if (! empty ( self::$plugins )) {
			
			return self::$plugins;
		}
		$dbo = Factory::getDbo ();
		$list = $dbo->query_list ( "SELECT * FROM {$dbo->table('plugins')} WHERE enable = 1" );
		self::$plugins = $list;
		return $list;
	}
	
	/**
	 * 引入单个指定插件
	 * 
	 * @param $plugin string       	
	 * @return object
	 */
	protected static  function _importPlugin($plugin) {
		$tempplugins=null;
		static $paths = array ();
		$plugin ['type'] = preg_replace ( '/[^A-Z0-9_\.-]/i', '', $plugin ['type'] );
		$plugin ['name'] = preg_replace ( '/[^A-Z0-9_\.-]/i', '', $plugin ['name'] );
		
		$path = ROOT . '/plugins/' . $plugin ['type'] . '/' . $plugin ['name'] . '/' . $plugin ['name'] . '.php';
		
		if (! isset ( $paths [$path] )) {
			if (file_exists ( $path )) {
				
				require_once $path;
				$paths [$path] = true;
				$subject = JSubject::getInstance ();
				$calss_name = 'Plg' . ucfirst($plugin ['type']) . ucfirst($plugin ['name']);
				if (class_exists ( $calss_name )) {
					
					$tempplugins = new $calss_name ( $subject, $plugin );
				}
				
			} else {
				
				$paths [$path] = false;
			}
		
		}
		
		return $tempplugins;
	}

}