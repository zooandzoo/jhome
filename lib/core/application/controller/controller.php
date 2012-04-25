<?php

/**
 * @author Administrator
 *	控制层
 *	在控制层获取其他自定义model 
 *	$this->newModel($model_name);
 */
class Controller extends BaseObject {
	
	/**
	 *
	 * @var array 消息
	 */
	protected $message = null;
	
	/**
	 *
	 * @var string 消息类型
	 */
	protected $messageType = null;
	
	/**
	 *
	 * @var string view层的实例对象
	 */
	protected $view = null;
	
	/**
	 *
	 * @var array 当前控制器公开的成员方法
	 */
	protected $methods = null;
	
	/**
	 *
	 * @var string 控制器跳转连接
	 */
	protected $redirect = null;
	
	/**
	 *
	 * @var string 组件根目录 /components/com_group
	 */
	protected $basepath = null;
	
	/**
	 *
	 * @var string 组件名称 group
	 */
	protected $component_name = null;
	
	/**
	 *
	 * @var string 改控制器名称 [Group]ControlerCroup
	 */
	protected $name = null;
	
	/**
	 *
	 * @var string 控制器任务映射数组
	 */
	protected $taskmap = array ();
	
	/**
	 *
	 * @var string 当前执行的task名称
	 */
	protected $currentTask = '';
	
	/**
	 *
	 * @var string model前缀 如:GroupModel
	 */
	protected $model_prefix = '';
	
	public static $instance = null;
	
	/**
	 *
	 * @var string //默认分发到组件试图的哪个文件夹(默认与组件名相同)	如Group
	 */
	protected $default_view = null;
	
	/**
	 * 视图文件存放路径
	 *
	 * @var string /components/com_group/views
	 *     
	 */
	protected $view_path = null;
	/**
	 * 模型文件存放路径
	 *
	 * @var string /components/com_group/models
	 */
	protected $model_path = null;
	
	public function __construct($config) {
		
		$this->methods = array ();
		$this->redirect = '';
		$this->name = $this->getName (); // Group
		                               
		$this->basepath = CURRENT_COMPONENT_PATH;   // /components/com_group
		
		// 如Group
		$this->component_name = $this->getName ();
		
		// 默认分发到组件试图的哪个文件夹 如Group
		$this->default_view = strtolower ( $this->getName () );
		
		// /components/com_group/views
		$this->view_path = CURRENT_COMPONENT_PATH . '/views';
		
		// /components/com_group/models
		$this->model_path = CURRENT_COMPONENT_PATH . '/models';
		
		// GroupModel
		$this->model_prefix = $this->getName () . 'Model';
		
		// 把路径存到函数里的静态变量paths中去，用的时候通过addModelPath(null, model_prefix);来取
		Model::addModelPath ( $this->model_path, $this->model_prefix );
		
		$methds = get_class_methods ( 'Controller' );
		$reflection_obj = new ReflectionClass ( $this );
		$rmethods = $reflection_obj->getMethods ( ReflectionMethod::IS_PUBLIC );
		foreach ( $rmethods as $rmethod ) {
			$methdsname = $rmethod->getName ();
			if (! in_array ( $methdsname, $methds ) || $methdsname == 'display') {
				$this->taskmap [strtolower ( $methdsname )] = $methdsname;
			}
		}
		
		$this->registerDefaultTask ( 'display' );
	
	}
	
	public function registerDefaultTask($default_task_name) {
		
		$this->taskmap ['__default'] = $default_task_name;
	
	}
	public static function getInstance($component, $config = array()) {
		
		if (isset ( self::$instance )) {
			
			return self::$instance;
		}
		
		$basepath = CURRENT_COMPONENT_PATH;
		$format = Request::getWord ( 'format' );
		$command = Request::getVar ( 'task', 'display' ); // 默认GETVAR按照string类型过滤
		
		if (strpos ( $command, '.' ) !== false) {
			
			//如果任务表示task以'.'的形式赋值执行那么包含相应的控制器并实例化
			// task=banner.add $type=banner $command=add
			
			
			list ( $type, $task ) = explode ( '.', $command );
			$file = self::createFileName ( 'controller', array ('name' => $type, 'format' => $format ) );
			$path = $basepath . '/controllers/' . $file;
			Request::setVar ( 'task', $task );
			
		} else {
			
			//如果任务表示task ，无 ‘.’ 那么包含默认的controller实例化， 并执行里面定义提供的方法
			
			$type = null;
			$task = $command;
			$file = self::createFileName ( 'controller', array ('name' => 'controller', 'format' => $format ) );
			$path = $basepath . '/controllers/' . $file;
		
		}
		$classname = ucfirst ( $component ) . 'Controller' . ucfirst ( $type );
		if (! class_exists ( $classname )) {
			if (file_exists ( $path )) {
				require_once $path;
			} else {
				
				exit ( 'controlers文件夹中的控制器文件不存在' );
			}
		}
		if (class_exists ( $classname )) {
			
			self::$instance = new $classname ( $config );
		}
		return self::$instance;
	}
	
	public function createFileName($type, $parts = array()) {
		switch ($type) {
			case 'controller' :
				if (! empty ( $parts ['format'] )) {
					if ($parts ['format'] == 'html') {
						
						$parts ['format'] = '';
					} else {
						
						$parts ['format'] = '.' . $parts ['format'];
					}
				
				} else {
					$parts ['format'] = '';
				
				}
				$filename = strtolower ( $parts ['name'] ) . $parts ['format'] . '.php';
				break;
			case 'view' :
				
				$filename = strtolower ( $parts ['name'] ) . '/view.' . $parts ['type'] . '.php';
				break;
		}
		
		return $filename;
	}
	public function execute($task) {
		
		$this->currentTask = strtolower ( $task );
		if (isset ( $this->taskmap [$task] )) {
			
			$dotask = $this->taskmap [$task];
		} elseif (isset ( $this->taskmap ['__default'] )) {
			
			$dotask = $this->taskmap ['__default'];
		} else {
			
			exit ( '控制器方法未找到' );
		}
		
		$this->currentTask = $dotask;
		$this->$dotask ();
	}
	
	/**
	 * 跳转,如果有消息则添加到应用的消息队列中
	 * @return boolean
	 */
	public function redirect() {
		if ($this->redirect) {
			$app = Factory::getApplication ();
			$app->redirect ( $this->redirect, $this->message, $this->messageType );
		
		}
		return false;
	
	}
	protected function display() {
		
		$document = Factory::getDocument (); // 根据Request::getWordInt('format')来实例化相应文档类型
		                                     // format=json
		$viewType = $document->getType ();
		// 分发到相应试图文件夹
		$viewName = Request::getWordInt ( 'view', $this->default_view );
		// 查找对应的模板文件
		$viewLayout = Request::getWordInt ( 'layout', 'default' );
		// 可以当做smarty动态参数
		$array = array ('component' => strtolower ( $this->component_name ), 'viewName' => $viewName, 'viewLayout' => $viewLayout, 'suffix' => 'html' );
		
		$this->view = $this->getView ( $viewName, $viewType, '', $array );
		$this->view->assignRef ( 'document', $document );
		$this->view->getTemplateObj ()->assign ( 'document', $document );
		if ($model = $this->getModel ( $viewName )) {
			$this->view->setModel ( $model, true );
		}
		
		$this->view->display ();
	}
	
	protected function assign($var, $value = '') {
		if (empty ( $this->view )) {
			$this->view = $this->getView ();
		}
		$this->view->assign ( $var, $value );
	}
	
	protected function getView($viewName, $viewType, $prefix, $array = array()) {
		
		if (empty ( $viewName )) {
			$viewName = $this->getName ();
		}
		if (empty ( $prefix )) {
			$prefix = $this->getName () . 'View';
		}
		if (! $view = $this->createView ( $viewName, $viewType, $prefix, $array )) {
			exit ( '创建视图失败' );
		}
		
		return $view;
	
	}
	
	protected function createView($viewName, $viewType, $prefix, $array) {
		// Group
		$viewName = preg_replace ( '/[^A-Z0-9_]/i', '', $viewName );
		// json
		$viewType = preg_replace ( '/[^A-Z0-9_]/i', '', $viewType );
		// GRoupView
		$prefix = preg_replace ( '/[^A-Z0-9_]/i', '', $prefix );
		
		$classname = $prefix . ucfirst ( $viewName );
		if (! class_exists ( $classname )) {
			
			kimport ( 'core.filesystem.path' );
			$filename = $this->createFileName ( 'view', array ('name' => strtolower ( $viewName ), 'format' => strtolower ( $viewType ), 'type' => strtolower ( $viewType ) ) );
			$path = Path::find ( $this->view_path, $filename );
			if ($path) {
				
				require_once $path;
				if (! class_exists ( $classname )) {
					
					exit ( '试图入口类不存在' );
				}
			
			} else {
				return null;
			}
		
		}
		$view_object = new $classname ( $array );
		return $view_object;
	}
	
	/**
	 *
	 * @param $name string
	 *       	 view参数的值
	 * @param $viewname unknown_type       	
	 * @return boolean Ambigous unknown>
	 */
	protected function getModel($name = '', $prefix = '', $config = array()) {
		
		if (empty ( $name )) {
			
			$name = $this->getName ();
		}
		if (empty ( $prefix )) {
			
			$prefix = $this->model_prefix;
		}
		$model = $this->createModel ( $name, $prefix, $config );
		if (! $model) {
			return false;
		}
		
		return $model;
	}
	
	protected function createModel($name, $prefix, $config = array()) {
		$models = Model::getInstance ( $name, $prefix, $config = array () );
		return $models;
	}
	
	protected function getName() {
		if (empty ( $this->name )) {
			if (! preg_match ( '/(.*)Controller/i', get_class ( $this ), $match )) {
				exit ( '获取控制器名称失败' );
			}
			$this->name = $match [1];
		}
		return $this->name;
	}
	
	protected function setRedirect($url, $msg = NULL, $type = null) {
		$this->redirect = $url;
		if ($msg != null) {
			$this->message = $msg;
		}
		if (empty ( $type )) {
			if (empty ( $this->messageType )) {
				$this->messageType = 'message';
			}
		
		} else {
			$this->messageType = $type;
		}
	}
	public function holdEditId($context,$id){
		$app=Factory::getApplication();
		$data=(array)$app->getUserState($context.'.id');
		if(!empty($id)){
			array_push($data, (int)$id);
			$data=array_unique($data);
			$app->setUserState($context.'.id',$data);
		}
	}
	
	public function checkEditId($context,$id){
		
		if($id){
			$app=Factory::getApplication();
			$data=$app->getUserState($context.'.id');
			
			$result=in_array((int) $id, (array)$data);
			
			return $result;
		}else{
			
			return true;
		}
		
		
	}

}
?>