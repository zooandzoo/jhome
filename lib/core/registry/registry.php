<?php

/**
 * @author Administrator
 *
 *
 *注册对象类：把相应的数据注册为对象
 */
class Registry {
	
	/**
	 *
	 * @var object 注册对象别名,类型只为对象
	 */
	protected $data = NULL;
	/**
	 *
	 * @var array 已注册对象的存放容器
	 */
	protected static $instance = array ();
	
	/**
	 *
	 * @param $data mixed
	 * 要注册的数据，可以是数组、对象、字符串
	 */
	public function __construct($data = null) {
		// 新建对象
		$this->data = new stdClass ();
		
		if (is_array ( $data ) || is_object ( $data )) {
			// 如果是对象或数组
			
			$this->bindData ($this->data,$data );
		
		} elseif (is_string ( $data )) {
			// 如果是字符串
			
			$this->loadString ( $data );
		
		}
	
	}
	/**
	 * 获取对象
	 * @param $id string
	 * @return Registry multitype:
	 */
	public static function getInstance($id) {
	
		if (empty ( self::$instance [$id] )) {
				
			return self::$instance [$id] = new Registry ();
		}
	
		return self::$instance [$id];
	}
	
	
	
	/**
	 *
	 * @param $data mixed
	 *       	 绑定对象、或者数组
	 */
	protected function bindData(&$object,$data) {
		if (is_object ( $data )) {
			
			$data = get_object_vars ( $data );
		} else {
			
			$data = ( array ) $data;
		}
		
		foreach ( $data as $key => $value ) {
			if (is_array ( $value ) || is_object ( $value )) {
				$object->$key=new stdClass;
				$this->bindData ($object->$key, $value );
			} else {
				$object->$key = $value;
			}
		}
	
	}
	
	/**
	 *把字符串转换成对应对象
	 *应该支持多种字符串转换成多种类型如json,xml
	 *依赖RegistryFormat抽象类来实现
	 * @param $data string       	
	 */
	public function loadString($data,$format='JSON',$option=array()) {
	
		$handler=RegistryFormat::getInstance($format);
		
		$obj=$handler->stringToObject($data);
		//获取相应的对象后绑定到注册类的$data成员变量中
		$this->loadObject($obj);
	}
	/**
	 * 绑定数据到对象中去为bindData代理函数
	 * @param object $data 
	 */
	public function loadObject($data){
		
		$this->bindData($this->data, $data);
		
	}
	
	
	
	
	
	
	
	/**
	 *转换当前data成员变量为字符串
	 */
	public function toString($format='JSON',$option=array()){
		
		$handle=RegistryFormat::getInstance($format);
		return $handle->objecToString($this->data,$option=null);
		
		
	}
	/**
	 * 转换当前data成员变量为对象
	 */
	public function toObject(){
	
		return $this->data;
	}
	
	/**
	 * 转换当前data成员变量为数组
	 */
	public function toArray(){
		
		return (array)$this->asArray($this->data);
	}
	
	/**
	 *吧对象强制转换为数组 
	 * @param objeck $data
	 * @return array $array 
	 */
	public function asArray($data){
		$array=array();
		$data=get_object_vars($data);
		
		foreach($data as $key => $value){
			if(is_object($value))
			{
				
				$array[$key]=$this->asArray($value);
			}else{
				
				$array[$key]=$value;
			}
			
			
		}
		
		return $array;
	}

	
	
	
	/**
	 * 根据路径获取当前data值
	 */
	public function get($path,$default=null){
		$return_value=$default;
		
		if(!strpos($path,'.')){
			
			return (isset($this->data->$path)&&$this->data->$path!==null&&$this->data->$path!=='')?$this->data->$path:$default;
		}
		
		
		$paths=explode('.', $path);
		$path=$this->data;
		$found=false;
		if($paths){
			
			foreach($paths as $value){
				
				if(isset($path->$value)){
					$path=$path->$value;
					$found=true;
				}
				else{
					$found = false;
					break;
					
				}
			}
			
		}
		
		if($found&&$path!==null && $path !=''){
			
			$return_value = $path;
		}
	
		
		return $return_value;
	}
	
	/**
	 * 根据路径设置当前data值
	 */
	public function set($path,$value){
		$result = null;
		
		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;
		
			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
			{
			if (!isset($node->$nodes[$i]) && ($i != $n))
			{
			$node->$nodes[$i] = new stdClass;
			}
			$node = $node->$nodes[$i];
			}
		
			// Get the old value if exists so we can return it
			$result = $node->$nodes[$i] = $value;
		}
		
		return $result;
	}

}

