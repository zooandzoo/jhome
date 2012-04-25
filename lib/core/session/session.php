<?php
class Session extends BaseObject {
	protected $expire = 15;
	protected $state = 'active';
	protected $storage_handle=null;
	protected static $instance = null;
	protected $security = array ('fix_address', 'fix_browser' );
	
	/**
	 *
	 * @param $seesion_config_array array
	 *       	 session配置数组
	 */
	private function __construct($storage_handle='none',$seesion_config_array) {
		// 如果存在 session那么删除它
		if (session_id ()) {
			session_unset ();
			session_destroy ();
		}
		ini_set ( 'session.save_handler', 'files' );
		ini_set ( 'session.use_trans_sid', '0' );
		
		$this->storage_handle=SessionStorage::getInstance($storage_handle,$seesion_config_array);
		// 设置session 名称和过期时间
		$this->setOption ( $seesion_config_array );
		// 设置cookie 路径和域名
		$this->setCookieParams ();
		// 启动session
		$this->sessionStart ();
		// 设置session时间属性
		$this->setTime ();
		$this->setCounter();
		// 验证session并设置诸如session.client.address属性
		$this->validate ();
	
	}
	public function setCounter(){
		$counter=$this->get('session.counter',0);
		++$counter;
		$this->set('session.counter',$counter);
		return true;
		
	}
	public function validate($restart = false) {
		
		if ($restart) {
			// 重启session
		
		}
		// 验证是否过期
		if ($this->expire) {
			$curtime = $this->get ( 'session.time.now', 0 );
			$maxtime = $this->get ( 'session.time.last', 0 ) + $this->expire;
			if ($maxtime < $curtime) {
				$this->state = 'expired';
				return false;
			
			}
		
		}
		// 如果用户使用代理
		if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
			
			$this->set ( 'session.client.forwarded', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
		}
		
		
		if (in_array ( 'fix_address', $this->security ) && isset ( $_SERVER ['REMOTE_ADDR'] )) {
			
			$ip = $this->get ( 'session.client.address' );
			
			if ($ip == null) {
				$this->set ( 'session.client.address', $_SERVER ['REMOTE_ADDR'] );
			
			} elseif ($_SERVER ['REMOTE_ADDR'] !== $ip) {
				// 当用户操作过程中更换了IP那么SESSION失效
				$this->state = 'error';
				return false;
			}
		
		}
		
		if (in_array ( 'fix_browser', $this->security ) && isset ( $_SERVER ['HTTP_USER_AGENT'] )) {
			$browser = $this->get ( 'session.client.browser' );
			
			if ($browser === null) {
				$this->set ( 'session.client.browser', $_SERVER ['HTTP_USER_AGENT'] );
			} elseif ($_SERVER ['HTTP_USER_AGENT'] !== $browser) {
				$this->state = 'error';
				return false;
			}
		
		}
	
	}
	
	/**
	 * 设置session请求时间参数
	 */
	public function setTime() {
		$start = time ();
		if (! $this->has ( 'session.timer.start' )) {
			
			// session开始时间
			$this->set ( 'session.timer.start', $start );
			// 上次访问时间
			$this->set ( 'session.timer.last', $start );
			// 想在请求时间
			$this->set ( 'session.timer.now', $start );
		}
		
		$this->set ( 'session.timer.last', $this->get ( 'session.timer.now' ) );
		$this->set ( 'session.timer.now', $start );
		return TRUE;
	
	}
	
	public function set($name, $value = null, $namespace = 'default') {
		
		$namespace = '__' . $namespace;
		
		if ($this->state !== 'active') {
			
			return null;
		}
		$old = isset ( $_SESSION [$namespace] [$name] ) ? $_SESSION [$namespace] [$name] : null;
		if ($value == null) {
			// 清空值
			unset ( $_SESSION [$namespace] [$name] );
		
		} else {
			
			$_SESSION [$namespace] [$name] = $value;
		}
	
	}
	
	/*
	 * @param string $name 属性名称 @param string $default 默认值如果获取不到返回默认 @return
	 * mixed 值
	 */
	public function get($name, $default = null, $namespace = 'default') {
		$namespace = '__' . $namespace;
		if ($this->state !== 'active' && $this->state !== 'expired') {
			
			return null;
		
		}
		
		if (isset ( $_SESSION [$namespace] [$name] )) {
			
			return $_SESSION [$namespace] [$name];
		} else {
			
			return $default;
		}
	
	}
	
	/**
	 *
	 * @param $name string       	
	 * @param $defaultname string       	
	 * @return NULL
	 */
	public function has($name, $defaultname = 'default') {
		$namespace = '__' . $defaultname;
		if ($this->state != 'active') {
			
			return null;
		}
		return isset ( $_SESSION [$namespace] [$name] );
	
	}
	
	public function sessionStart() {
		if($this->state=='restart'){
			session_id($this->_createId());	
		}else{
			
			
		}
		
		
		$name = session_name ();
		
		session_start ();
	}
	public function setOption($seesion_config_array) {
		session_name ( $seesion_config_array ['name'] );
		if (empty ( $seesion_config_array ['lifetime'] )) {
			
			$seesion_config_array ['lifetime'] = $this->expire;
		}
		ini_set ( 'session.gc_maxlifetime', $seesion_config_array ['lifetime'] );
	
	}
	
	public function isNew(){
		$count=$this->get('session.counter');
		if($count===1){
			
			return true;
		}
		return false;
	}
	public function setCookieParams() {
		$config = Factory::getConfig ();
		$cookie = session_get_cookie_params ();
		if ($config->cookie_path != '') {
			
			$cookie ['path'] = $config->cookie_path;
		}
		if ($config->cookie_domain != '') {
			
			$cookie ['domain'] = $config->cookie_domain;
		}
		session_set_cookie_params ( $cookie ['lifetime'], $cookie ['path'], $cookie ['domain'], $cookie ['secure'] );
	}
	
	public static function getInstance($storage_handle,$seesion_config_array) {
		if (empty ( self::$instance )) {
			
			self::$instance = new Session ($storage_handle,$seesion_config_array );
		}
		
		return self::$instance;
	
	}
	public function getState(){
		
		return $this->state;
	}
	
	public function getToken($forceNew=false){
		$token=$this->get('session.token');
		if($token===null||$forceNew){
			
			$token=$this->_createToken(12);
			$this->set('session.token',$token);
		}
		return $token;
	}
	
	protected function _createToken($length = 32)
	{
		static $chars = '0123456789abcdef';
		$max = strlen($chars) - 1;
		$token = '';
		$name = session_name();
		for ($i = 0; $i < $length; ++$i)
		{
			$token .= $chars[(rand(0, $max))];
		}
	
		return md5($token . $name);
	}
	
	public static function getFormToken($forceNew=false){
		$session=Factory::getSession();
		$hash=Application::getHash($session->getToken($forceNew));
		return $hash;
	}
	
	public function destroy(){
		if($this->state==='destroyed'){
			
			return true;
		}
		if($_COOKIE[session_name()]){
			$config = Factory::getConfig ();		
				$cookie ['path'] = $config->cookie_path;			
				$cookie ['domain'] = $config->cookie_domain;
	
			setcookie(session_name(),'',time()-42000,$cookie ['path'],$cookie ['domain']);
		}
		
		session_unset();
		session_destroy();
		$this->state='destroyed';
		return true;
	}
	

	public function restart(){
		$this->destroy();
		if($this->state!='destroyed'){
			return false;	
		}
		
		$this->register();
		$this->state='restart';
		session_id($this->_createId());
		$this->sessionStart();
		$this->state='active';
		//过期和安全验证
		$this->validate();
		$this->setCounter();
		
	}
	protected function _createId(){
		$id = 0;
		while (strlen($id) < 32)
		{
			$id .= mt_rand(0, mt_getrandmax());
		}
		
		$id = md5(uniqid($id, true));
		return $id;
		
	}
	public function register(){
		
		$this->storage_handle->register();
	}
	public function getName(){
		
		return session_name();
		
	}
	public function getId(){
		
		return session_id();
	}

}

?>