<?php

/**
 * @author Administrator
 *	表单通用控制层
 *	在控制层获取其他自定义model 
 *	$this->newModel($model_name);
 */
class ControllerForm extends Controller {
	
	/**
	 * @var string 控制器名称后缀部分 如ConfigControlerSpam 的Spam
	 */
	protected $context='';
	
	/**
	 * @var string 组件名称
	 */
	protected $option='';
	
	
	/**
	 * @var string view参数
	 */
	protected $view='';
	
	
	public function __construct($config){
		parent::__construct($config);
		$this->option='com_'.strtolower($this->getName());
		if(empty($this->context)){
			if(!preg_match('/(.*)Controller(.*)/i', get_class($this),$array)){
				exit('获取控制器上下文错误，检测控制器名称');
			}
			$this->context=strtolower($array[2]);
		}
		$this->view=strtolower($this->context);
		
	}
	
	
	/* 重载getmodel函数
	 * @see Controller::getModel()
	 */
	public function getModel($name='',$prefix='',$config=''){
		if(empty($name)){
			$name=$this->context;
		}
		return parent::getModel($name);
		
	}
	


}	