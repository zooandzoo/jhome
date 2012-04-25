<?php
class DocumentHTML extends Document {
	
	/**
	 *
	 * @var string 模板目录名称
	 */
	public $template_name = '';
	/**
	 *
	 * @var string 模板属性
	 */
	public $params = array ();
	/**
	 *
	 * @var string 模板加载后的内容
	 */
	protected $template_content = '';
	
	/**
	 *
	 * @var array 模板自定义标签
	 */
	protected $_template_tags = array ();
	public $file = '';
	
	public function __construct($option = array()) {
		
		parent::__construct ( $option );
		$this->_type = 'html';
		$this->setMimeEncoding ( 'text/html' );
	
	}

	
	public function render($params) {
		$template_obj = Factory::getTemplateObject ();
		$document = Factory::getDocument ();
		$template_obj->compile_dir = CURRENT_APP_PATH . '/data/templates_c';
		$template_obj->cache_dir = CURRENT_APP_PATH . '/data/templates_static';
		$template_obj->template_dir = CURRENT_APP_PATH . '/templates/' . $params ['template'];
		$template_obj->assign ( 'documnet', $document );
		try {
			$conent = $template_obj->fetch ( $params ['file'] );
		} catch ( EXception $e ) {
			
			exit ( $e->getMessage () );
		}
		//给RESPONE模块添加相应的文档类型mime
		parent::render ();
		return $conent;
	}
	
	public function setBuffer($string, $type) {
		
		parent::$buffer [$type] = $string;
		return $this;
	
	}

}