<?php

class Application {
	public static $clients = array ();
	protected $appname = '';
	protected $clientId = null;
	public $requestTime = null;
	public $input = null;
	protected $messageQueue = array ();
	public static $instance = array ();
	
	public function __construct($config = array()) {
		
		$this->appname = $this->getName ();
		$this->requestTime = time ();
		if (! isset ( $config ['session'] )) {
			$config ['session'] = true;
		}
		if (isset ( $config ['clientId'] )) {
			$this->clientId = $config ['clientId'];
		}
		if (! isset ( $config ['session_name'] )) {
			$config ['session_name'] = $this->appname;
		}
		if (! isset ( $config ['config_file'] )) {
			
			$config ['config_file'] = 'config_global.php';
		}
		if (file_exists ( ROOT . '/config/' . $config ['config_file'] )) {
			
			$this->creatConfig ( ROOT . '/config/' . $config ['config_file'] );
		}
		
		if ($config ['session'] != false) {
			
			self::createSession ( $config ['session_name'] );
		}
	
	}
	public static function createSession($sessionname = '') {
		$name = self::getHash ( $sessionname );
		$options = array ('name' => $name );
		$session = Factory::getSession ( $options );
		if($session->isNew())
		{
			// 检查用户SESSION,如果SESSION 并初始化USER对象存到SESSION中
			self::checkSession ();
		}
		return $session;
	
	}
	
	public function checkSession() {
		$session = Factory::getSession ();
		// 获取USER对象，先从SESSION中获取，没有则创建
		$user = Factory::getUser (null,'',true);
		$re = new Registry ();
		
		if ($session->isNew ()) {
			$session->set ( 'state', $re );
			$session->set ( 'user', $user );
		}
	}
	public static function getInstance($name = '', $config = array()) {
		
		if (empty ( self::$instance [$name] )) {
			// 获取应用配置信息(固定的)
			$appinfo = self::getInfo ( $name );
			
			$include_app = $appinfo->path . '/include/application.php';
			
			if (file_exists ( $include_app )) {
				
				include_once $include_app;
				$classname = ucfirst ( $name );
				$apponj = new $classname ( $config );
				self::$instance [$name] = &$apponj;
			}
		}
		return self::$instance [$name];
	}
	
	/**
	 * 创建 应用config存到工厂对象中去
	 * 
	 * @param $file string       	
	 * @return object Config
	 */
	public function creatConfig($file) {
		return Factory::getConfig ( $file );
	}
	public function init() {
		// 此方法进行语言加载，等操作
	
	}
	public function router() {
		// 此方法可以来解析是否是ssl 'https'然后进行跳转
		$uriobj = URI::getInstance ();
	
	}
	
	public function dispatch() {
		$component = Request::getVar ( 'option' ); // request::getword('option')
		$document = Factory::getDocument ();
		
		$content = Components::displayComponents ( $component );
		$document->setBuffer ( $content, 'main' );
	
	}
	
	public function render() {
		$tmpl = $this->getTemplate ();
		$tmpl_params = array ('template' => $tmpl->tplname, 'file' => 'index.html', 'directory' => THEMS, 'params' => $tmpl->param );
		$doucment = Factory::getDocument ();

		//根据文档类型的不同，继承的文档类型需要覆盖render方法来渲染相应的数据格式，如DocumentHTML用的是SMARTY输出HTML字符串
		$data = $document->render ( $tmpl_params );
		Response::setBody ( $data );
	
	}
	
	/**
	 *
	 * @return object
	 */
	public function getTemplate() {
		
		static $template;
		if (! isset ( $template )) {
			
			$tmpobj = new stdClass ();
			$tmpobj->param = array ('color' => 'blue', 'fontsize' => 2 );
			
			$tmpobj->tplname = 'blue';
		
		}
		
		return $tmpobj;
	
	}
	
	public function getName() {
		$name = $this->appname;
		if (empty ( $name )) {
			$name = get_class ( $this );
			$name = strtolower ( $name );
		}
		
		return $name;
	}
	public function __toString() {
		$config = Factory::getConfig ();
		return Response::toString ( $config->gzip );
	}
	
	/**
	 * 获取应用信息，没有则创建
	 * 
	 * @param $name string       	
	 * @return multitype: unknown NULL
	 */
	public static function getInfo($name) {
		
		if (self::$clients == null) {
			// 前台应用
			$obj = new stdClass ();
			$obj->id = 0;
			$obj->name = 'site';
			$obj->path = CURRENT_APP_PATH;
			self::$clients [0] = $obj;
			
			// 后台应用
			$obj = new stdClass ();
			$obj->id = 1;
			$obj->name = 'admin';
			$obj->path = CURRENT_APP_PATH;
			self::$clients [1] = $obj;
		
		}
		if (is_null ( $name )) {
			
			return self::$clients;
		}
		foreach ( self::$clients as $client ) {
			if ($client->name == strtolower ( $name )) {
				
				return $client;
			}
		
		}
		return null;
	}
	
	/**
	 * 应用信息跳转
	 * 
	 * @param $url string       	
	 * @param $mas string       	
	 * @param $msgType string       	
	 * @param $movied string       	
	 */
	public function redirect($url, $message = '', $msgType = 'message', $moved = false) {
		
		$url = preg_split ( "/[\r\n]/", $url );
		$url = $url [0];
		
		if (trim ( $message )) {
			$this->enqueueMessage ( $message, $msgType );
		}
		
		$session = Factory::getSession ();
		if (count ( $session->get ( 'application.queue' ) )) {
			$session->set ( 'application.queue', null );
		}
		$session->set ( 'application.queue', $this->messageQueue );
		if (headers_sent ()) {
			
			echo "<script>document.location.href='" . htmlspecialchars ( $url ) . "';</script>\n";
		} else {
			$document = Factory::getDocument ();
			header ( $moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other' );
			header ( 'Location: ' . $url );
			header ( 'Content-Type: text/html; charset=' . $document->getCharset () );
		}
		$this->close ();
	
	}
	public function close($code = 0) {
		exit ( $code );
	}
	public function enqueueMessage($message, $type = 'message') {
		//
		
		// 如果有信息直接添加
		$this->messageQueue [] = array ('message' => $message, 'type' => strtolower ( $type ) );
	}
	
	
	/**
	 * 返回应用错误数组
	 * @param string $message
	 * @param string $type
	 * @return array
	 */
	public  function getMessageQueue($message, $type) {
		
		return $this->messageQueue;
	}
	
	
	
	/**
	 * 返回一条错误信息
	 * @return multitype:
	 */
	public  function getMessage(){
		if(!empty($this->messageQueue)){
			
			return $this->messageQueue [0];
		}
		
	}
	public static function getHash($string) {
		
		return md5 ( Factory::getConfig ()->secret . $string );
	
	}
	
	public function getUserState($path, $default = null) {
		
		$session = Factory::getSession ();
		$registry = $session->get ( 'state' );
		if (! is_null ( $registry )) {
			
			return $registry->get ( $path );
		}
		return $default;
	}
	
	public function setUserState($path, $value) {
		$session = Factory::getSession ();
		$registry = $session->get ( 'state' );
		if (! is_null ( $registry )) {
			
			return $registry->set ( $path, $value );
		}
		return $default;
	
	}
	
	/**
	 * 应用登录，依赖登录模块
	 * 
	 * @param $input array       	
	 *
	 * @return array 相应数组信息
	 */
	public function Login($input) {
		
		kimport ( 'core.user.auth' );
		$authobject = new Auth ();
		$response = $authobject->auth ( $input );
		$user = Factory::getUser ();
		// 如果回去用户信息成功那么把用户信息绑定到用户对象中,否则直接返回错误给应用
		if ($response ['status'] === 'success') {
			$user_obj = $user->getInstance ( $response ['userinfo'] ['uid'] );
			$session = Factory::getSession ();
			$session->set ( 'user', $user_obj );
		}
		
		return $response;
	}
	
	
	

}
?>