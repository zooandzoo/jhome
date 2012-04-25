<?php

/**
 * @author Administrator
 * model层
 * 在model获取其他自定义model对象方法
 *	  $this->newModel($model_name);
 */
class Model extends BaseObject {
	
	/**
	 *
	 * @var object DBO
	 */
	protected $dbobject = null;
	
	/**
	 *
	 * @var string 与view参数的值相同,第一个字母大写
	 */
	protected $name = '';
	
	/**
	 *
	 * @var string model对象集
	 */
	protected static $instance = null;
	
	private function __construct() {
		
		$this->dbobject = Factory::getDbo ();
		
		if (empty ( $this->name )) {
			
			$this->name = $this->getName (); // 与view参数的值相同 第一个字母大写
		}
	}
	
	/**
	 *
	 * @param $type string
	 *       	 view 参数值
	 * @param $prefix string
	 *       	 形如bannerModel
	 * @param $config string
	 *       	 初始化配置参数
	 */
	public static function getInstance($viewname, $prefix, $config = array()) {
		if (! empty ( self::$instance [$viewname] )) {
			return self::$instance [$viewname];
		}
		
		$type = preg_replace ( '/[^A-Z0-9_\.-]/i', '', $viewname );
		$classname = $prefix . ucfirst ( $type );
		if (! class_exists ( $classname )) {
			kimport ( 'core.filesystem.path' );
			kimport ( 'core.application.model.model' );
			$path = Path::find ( Model::addModelPath ( null, $prefix ), Model::createFileName ( $type ) );
			
			if ($path) {
				require_once $path;
			
			} else {
				
				return false;
			}
		}
		self::$instance [$viewname] = new $classname ( $config );
		return self::$instance [$viewname];
	}
	public static function createFileName($name) {
		
		$file = $name . '.php';
		return $file;
	}
	
	public function getName() {
		
		if (empty ( $this->name )) {
			$r = null;
			if (! preg_match ( '/Model(.*)/i', get_class ( $this ), $r )) {
				
				exit ( '获取MODEL名称失败(不规范)' );
			}
			
			$this->name = strtolower ( $r [1] );
		}
		return $this->name;
	
	}
	public function newModel($viewname, $prefix, $config = array()) {
		
		return self::getInstance ( $viewname, $prefix );
	}
	
	public function getError() {
		
		return $this->_errors;
	}
	public static function addModelPath($path = '', $name = '') {
		
		static $paths;
		
		if (! isset ( $paths )) {
			
			$paths = array ();
		}
		
		if (! isset ( $paths [$name] )) {
			
			$paths [$name] = array ();
		}
		
		if (! in_array ( $path, $paths ) && $path != null) {
			
			$paths [$name] = $path;
		}
		
		return $paths [$name];
	
	}
	
	public function getDbo() {
		
		return $this->dbobject;
	
	}
	/**
	 *
	 * @param $data array
	 *       	 POST或者GET数组
	 *       	
	 *       	 数组属性：
	 *       	 'name' 表单字段name属性值
	 *       	 'filter' 采用哪种类型过滤，默认string
	 *       	 'required' 是否必填
	 *       	 'size' 值的长度
	 *       	 'filter_function' 自定义过滤函数
	 *       	 'validate' 自定义验证函数
	 *       	 'description' 字段的中文名称
	 * @return array 返回验证字段      	
	 */
	public function xml_Validate($data, $validate_rules = '') {
		
		$path = CURRENT_COMPONENT_PATH . '/models/form_validate';
		$return_value = array ();
		$model_suffix = $this->getName ();
		$register = new Registry ();
		$xml_str='';
		$register->loadObject ( $data );
		$fullpath = $path . '/' . $model_suffix . '.xml';
		if(!empty($validate_rules)&&!file_exists($fullpath)){
			$field_str = '';
			foreach ( $validate_rules as $key => $value ) {
				$attr_str = '';
				foreach ( $value as $k => $v ) {
					$attr_str .= $k . ' = "' . $v . '" ';
				}
				$field_str .= '<field ' . $attr_str . '/>';
			}
				
			$xml_str = '<?xml version="1.0" encoding="utf-8"?><validate>' . $field_str . '</validate>';
			
			
			$xml_obj = simplexml_load_string ( $xml_str );
		}else if(file_exists($fullpath)){
			
			$xml_obj = simplexml_load_file ( $fullpath );
			
			
		}else{
			
			exit('需要提供验证规则对字段验证');
		}
		
		
		
		if ($xml_obj instanceof SimpleXMLElement) {
			$field = $xml_obj->xpath ( '/validate/field' );
			
			if (! empty ( $field )) {
				foreach ( $field as $key => $value ) {
					$xml_field_attr = array ('name' => ! empty ( $field [$key] ['name'] ) ? ( string ) $field [$key] ['name'] : '', 
							'filter' => ! empty ( $field [$key] ['filter'] ) ? ( string ) $field [$key] ['filter'] : '',
							 'required' => ( string ) $field [$key] ['required'] == 'true' ? true : '',
							 'size' => ! empty ( $field [$key] ['size'] ) ? ( string ) $field [$key] ['size'] : 0, 
							'description' => ! empty ( $field [$key] ['description'] ) ? ( string ) $field [$key] ['description'] : '', 
							'validate' => ! empty ( $field [$key] ['validate'] ) ? ( string ) $field [$key] ['validate'] : '' );
				
					if (! empty ( $xml_field_attr ['name'] )) {
					
						// 通过表单name值获取要过滤的值也就是用户填写的值
						$need_filter = $register->get ( $xml_field_attr ['name'] );

							if (is_object($need_filter)) {
								
								//如果需要过滤的值是一个数组的话那么遍历过滤
								$temp = array ();
								foreach ( $need_filter as $key => $value ) {
									$headle_value = $this->filter ( $value, $xml_field_attr );
									$temp [$key] = $headle_value;
									$register->set ( $xml_field_attr ['name'], $temp );
								}
							} else {
								
								//不是数组直接遍历
								$headle_value = $this->filter ( $need_filter, $xml_field_attr );
								$register->set ( $xml_field_attr ['name'], $headle_value );
							}

					}
				}
			}
		} else {
			
			exit ( '验证XML格式错误或文件不存在' );
		}
		$return_value = $register->toArray ();
		return $return_value;
	
	}
	

	
	/**
	 *进行过滤
	 * @param $need_filter mixed       	
	 * @param $xml_field_attr array       	
	 * @return mixed
	 */
	public function filter($need_filter, $xml_field_attr) {
		
		if ($xml_field_attr ['required'] == true && empty ( $need_filter )) {
			$this->setError ( $xml_field_attr ['description'] . '为空' );
		}
		;
		
		$return = null;
		if (! empty ( $xml_field_attr ['size'] )) {
			if (! is_array ( $need_filter ) || ! is_object ( $need_filter )) {
				
				$need_filter = substr ( $need_filter, 0, $xml_field_attr ['size'] );
			}
		
		}
		switch (strtoupper ( $xml_field_attr ['filter'] )) {
			case 'INT_ARRAY' :
				if (is_object ( $need_filter )) {
					
					$value = get_object_vars ( $need_filter );
				}
				$value = is_array ( $value ) ? $value : array ($value );
				ArrayHelper::toInteger ( $value );
				$return = $value;
				break;
			
			case 'SAFEHTML' :
				$return = FilterInput::getInstance ( null, null, 1, 1 )->clean ( $value, 'string' );
				break;
			case 'URL' :
				if (empty ( $need_filter )) {
					return;
				}
				$value = FilterInput::getInstance ()->clean ( $need_filter, 'html' );
				$value = trim ( $value );
				$scheme = parse_url ( $value, PHP_URL_SCHEME );
				if (! $scheme) {
					$scheme = 'http:';
					$value = $scheme . '://' . $value;
				}
				$return = $value;
				
				return $return;
			
			case 'TEL' :
				$value = trim ( $need_filter );
				
				if (preg_match ( '/^(?:\+?1[-. ]?)?\(?([2-9][0-8][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/', $value ) == 1) {
					$number = ( string ) preg_replace ( '/[^\d]/', '', $value );
					if (substr ( $number, 0, 1 ) == 1) {
						$number = substr ( $number, 1 );
					}
					if (substr ( $number, 0, 2 ) == '+1') {
						$number = substr ( $number, 2 );
					}
					$result = '1.' . $number;
				} 				
				elseif (preg_match ( '/^\+(?:[0-9] ?){6,14}[0-9]$/', $value ) == 1) {
					$countrycode = substr ( $value, 0, strpos ( $value, ' ' ) );
					$countrycode = ( string ) preg_replace ( '/[^\d]/', '', $countrycode );
					$number = strstr ( $value, ' ' );
					$number = ( string ) preg_replace ( '/[^\d]/', '', $number );
					$result = $countrycode . '.' . $number;
				} 				
				elseif (preg_match ( '/^\+[0-9]{1,3}\.[0-9]{4,14}(?:x.+)?$/', $value ) == 1) {
					if (strstr ( $value, 'x' )) {
						$xpos = strpos ( $value, 'x' );
						$value = substr ( $value, 0, $xpos );
					}
					$result = str_replace ( '+', '', $value );
				
				} 			
				elseif (preg_match ( '/[0-9]{1,3}\.[0-9]{4,14}$/', $value ) == 1) {
					$result = $value;
				} 				
				else {
					$value = ( string ) preg_replace ( '/[^\d]/', '', $value );
					if ($value != null && strlen ( $value ) <= 15) {
						$length = strlen ( $value );
						if ($length <= 12) {
							$result = '.' . $value;
						
						} else {
							
							$cclen = $length - 12;
							$result = substr ( $value, 0, $cclen ) . '.' . substr ( $value, $cclen );
						}
					} 					
					else {
						$result = '';
					}
				}
				$return = $result;
				break;
			default :
				$value = trim ( $need_filter );
				$filter_arr = explode ( ':', $xml_field_attr ['filter'] );
				if (count ( $filter_arr ) > 1) {
					
					if (method_exists ( $this, $filter_arr [1] )) {
						$value = call_user_func ( array ($this, $filter_arr [1] ), $value );
					} else {
						exit ( '过滤方法不存在,请在自定义model添加该方法' );
					}
				} else {
					
					$return = $value = FilterInput::getInstance ()->clean ( $value, $xml_field_attr ['filter'] );
				}
		}
		if (! empty ( $xml_field_attr ['validate'] )) {
			$name = $xml_field_attr ['validate'];
			$com_file = CURRENT_COMPONENT_PATH . '/models/rules/' . $name . '.php';
			if (file_exists ( $com_file )) {
				require_once $com_file;
			}
			$file = FRAME_CORE . '/application/model/rules/' . $name . '.php';
			if (file_exists ( $file )&&!file_exists($com_file)) {
				require_once $file;
			}
				
			if (function_exists ( $name )) {
				$return = call_user_func ( $name, $value );
				if (! $return) {
					$this->setError ( $xml_field_attr ['description'].'不正确');
				}
			} else {
				exit ( '验证方法不存在,请在core/application/model/rules或[当前组件]/model/rules 创建该验证文件' );
			}
		}
		
		
		
		return $return;
	}

}

?>