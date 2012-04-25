<?php

class View extends BaseObject {
	
	/**
	 *
	 * @var string 前缀部分ConfigViewPrivacy的config 与组件名称相同
	 *
	 */
	protected $name = null;
	
	/**
	 *
	 * @var string 默认布局
	 */
	protected $layout = 'default';
	
	/**
	 *
	 * @var string 模板后缀
	 */
	protected $layoutExt = 'html';
	
	/**
	 *
	 * @var object 模板对象
	 */
	protected $template_object = null;
	
	/**
	 *
	 * @var string 字符集
	 */
	protected $charset = 'UTF-8';
	
	/**
	 * @var array 挂载的变量
	 *
	 */
	protected $var = null;
	
	
	/**
	 * @var array model对象数组，可存放多个不同的model
	 */
	protected $_models = array ();
	
	
	/**
	 * @var string 默认model名称
	 */
	protected $default_model_name = null;
	
	
	/**
	 * @var object 文档对象
	 */
	protected $document = null;
	
	public function __construct($option_array) {
		
		if (! empty ( $option_array )) {
			$this->template_object = Factory::getTemplateObject ();
			$this->template_object->left_delimiter = '<!--{';
			$this->template_object->right_delimiter = '}-->';
			$this->template_object->compile_dir = CURRENT_APP_PATH . '/data/templates_c/components/' . 'com_' . $option_array ['component'] . '/' . $option_array ['viewName'];
			$this->template_object->caching = 0;
			$this->template_object->cache_dir = CURRENT_APP_PATH . '/data/templates_static/components/' . 'com_' . $option_array ['component'] . '/' . $option_array ['viewName'];
			$this->template_object->template_dir = CURRENT_APP_PATH . '/components/' . 'com_' . $option_array ['component'] . '/views/' . $option_array ['viewName'].'/tmpl';
			$this->name = $option_array ['component'];
			$this->layout = $option_array ['viewLayout'];
			$this->layoutExt = $option_array ['suffix'];
		
		}
	}
	public function get($property, $default = null) {
		if (is_null ( $default )) {
			
			$model = $this->default_model_name;
		} else {
			
			$model = strtolower ( $default );
		}
		if (isset ( $this->_models [$model] )) {
			
			$method_name = 'get' . ucfirst ( $property );
			
			if (method_exists ( $this->_models [$model], $method_name )) {
			
				$result=$this->_models [$model]->$method_name();
				return $result;
			}
		
		}
	
		$result=parent::get($property, $default);
		return $result;
	}
	public function setModel(&$Model, $default = FALSE) {
		$name = strtolower ( $Model->getName() ); // 与view参数的值相同
		$this->_models [$name] = &$Model;
		if ($default) {
			$this->default_model_name = $name;
		
		}
		
		return $Model;
	}
	public function assignRef($key, &$val) {
		
		if (is_string ( $key )) {
			$this->$key = &$val;
			return true;
		}
		
		return false;
	}
	public function assign($var, $value = '') {
		
		if (is_array ( $var )) {
			$this->var = array_merge ( $this->var, $var );
		
		} else if (is_object ( $var )) {
			
			foreach ( $var as $key => $value ) {
				
				$this->var [$var] = $value;
			}
		} else {
			
			$this->var [$var] = $value;
		}
		$this->template_object->assign ( $var, $value );
	
	}
	
	public function display($template='') {
		if (empty($template)){
			//如果为空那么自动根据layout参数来显示模板
			$template=$this->layout.'.'.$this->layoutExt;
		}
	try{
			$this->template_object->display ( $template );
		}catch (Exception $e){
			exit($e->getMessage());
		}
	
	}
	
	public function getTemplateObj(){
		
		return $this->template_object;
	}
	
	public function getFormStatic($filename) {
		if (! empty ( $filename )) {
			$file = CURRENT_COMPONENT_PATH . '/models/fields/' . $filename . '.php';
			if (file_exists ( $file )) {
	
			return	require_once $file;
			} else {
	
				exit ( 'FORM 静态表单字段不存在' );
			}
		}
	
	}
	
	/**
	 * 新建MODEL
	 * @param string $name 与view参数相同
	 * @return object 
	 */
	public function newModel($name){
		//$name与view参数相同
		//默认MODEL前缀如ConfigModel[view参数]
		if(!empty($this->_models[$name])){
				
				return $this->_models[$name];
		}
		$model_prefix=ucfirst($this->default_model_name).'Model';
		$model_obj=Model::getInstance($name, $model_prefix);
		if(!$model_obj){
				
			exit('自定义'.$name.'Model不存在');
		}
		$this->setModel($model_obj);

		return $model_obj;
	}
	public function validate($suffix_model){
		
		if(!empty($this->_models[$suffix_model])){
			
			$this->_models[$suffix_model]->validate();
		}
		
		
	}
	
	
	
	
}

?>