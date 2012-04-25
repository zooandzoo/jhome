<?php
class Document extends BaseObject {
	/**
	 *
	 * @var string 标题
	 */
	public $title = '';
	/**
	 *
	 * @var string 描述
	 */
	public $description = '';
	/**
	 *
	 * @var string 字符
	 */
	public $_charset = 'utf-8';
	/**
	 *
	 * @var string 文档类型
	 */
	public $_mime = '';
	/**
	 *
	 * @var array JS代码库
	 */
	public $_scripts = array ();
	/**
	 *
	 * @var array JS代码字符串
	 */
	public $_script = array ();
	/**
	 *
	 * @var array CSS代码库
	 */
	public $_styleSheets = array ();
	/**
	 *
	 * @var array CSS代码字段
	 */
	public $_style = array ();
	/**
	 *
	 * @var array meta标签数组
	 */
	public $_metaTags = array ();
	/**
	 *
	 * @var string 文档类型
	 */
	public $_type = null;
	/**
	 *
	 * @var array link标签
	 */
	public $_link = array();
	/**
	 *
	 * @var string 换行符
	 */
	public $_linEnd = "\12";
	
	/**
	 * @var string TAB缩进
	 */
	public $_tab="\11";
	
	
	/**
	 * @var string base标签
	 */
	public $base='';
	
	/**
	 *
	 * @var mixed 组件以及模块等输出缓冲
	 */
	public static $buffer = null;
	/**
	 *
	 * @var array 组件以及模块等文档对象
	 */
	public static $instance = null;
	
	public function __construct($option = array()) {
		
		if (array_key_exists ( 'linend', $option )) {
			$this->setLineEnd ( $option ['lineend'] );
		}
		if (array_key_exists ( 'charset', $option )) {
			
			$this->setCharset ( $option ['charset'] );
		}
		if (array_key_exists ( 'tab', $option )) {
			
			$this->setTab ( $option ['tab'] );
		}
		if (array_key_exists ( 'link', $option )) {
				
			$this->setLink ( $option ['link'] );
		}
		if (array_key_exists ( 'base', $option )) {
				
			$this->setBase ( $option ['base'] );
		}
	
	}
	public function setType($type){
		$this->_type=$type;
		return $this;	
	}
	public function getType(){
		
		return $this->_type;
	}
	public function getBase(){
		return $this->base;
	}
	
	public function setBase($base=''){	
		$this->base=$base;
	}
	public function setTitle($titlestrng){
		
		$this->title=$titlestrng;
	}
	public function getTitle(){
		
		return $this->title;
	}
	public function getLineEnd(){
		
		return $this->_linEnd;
	}
	public function getTab(){
		
		return $this->_tab;
	}
	public function fetchHeader(){
		
		$newline=$this->getLineEnd();
		$tab=$this->getTab();
		$buffer='';
		$tagEnd='/>';
		$base=$this->getBase();
		//base标签解析
		if(!empty($base)){
			
			$buffer .= '<base href="'.$this->getBase().'"/>'.$tagEnd;
		}
		
		//meta标签解析
		foreach ($this->_metaTags as $type=>$tag){
			foreach ($tag as $name => $content){
				
				if($type =='http-equiv'){
					$content .='; charset='.$this->_charset;
					$buffer .=$tab.'<meta http-equiv= "'.$name . '" content="'.htmlspecialchars($content).'"/>'.$newline;	
				}elseif($type=='standard'&&!empty($content)){
					
					$buffer .= $tab .'<meta name="'. $name .'" content="' .htmlspecialchars($content).'"/>' .$newline;
				}		
			}	
		}
		//描述标签解析
		$description=$this->getDescription();
		if($description){
			$buffer .= $tab.'<meta name= "description" content = "'.htmlspecialchars($description).'"/>'.$newline;
		}

		//link 外连接解析
		foreach ($this->_link as $link => $linkAtrr){
			
			$buffer .='<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';
			if ($temp = ArrayHelper::toString($linkAtrr['attribs']))
			{
				$buffer .= '' . $temp;
			}
			$buffer .= ' />' . $newline;
			
		}
		//css 外链接解析
		foreach($this->_styleSheets as $src=>$attr){
			$buffer .= $tab . '<link rel="stylesheet" href="' . $src . '" type="' . $attr['mime'] . '"';
			if (!is_null($attr['media']))
			{
				$buffer .= ' media="' . $attr['media'] . '" ';
			}
			if ($temp = ArrayHelper::toString($attr['attribs']))
			{
				$buffer .= ' ' . $temp;
			}
			$buffer .= $tagEnd . $newline;
			
		}
		
		
		//css 代码解析
		 foreach($this->_style as $type=>$content){
		 	$buffer .= $tab . '<style type="' . $type . '">' . $newline;
		 	// XHTML 
		 	if ($this->_mime != 'text/html')
		 	{
		 		$buffer .= $tab . $tab . '<![CDATA[' . $newline;
		 	}
		 	
		 	$buffer .= $content . $newline;
		 	
		 	
		 	if ($this->_mime != 'text/html')
		 	{
		 		$buffer .= $tab . $tab . ']]>' . $newline;
		 	}
		 	$buffer .= $tab . '</style>' . $newline;
			
		}
		
		//js 文件链接解析
		foreach ($this->_scripts as $strSrc => $strAttr)
		{
			$buffer .= $tab . '<script src="' . $strSrc . '"';
			if (!is_null($strAttr['mime']))
			{
				$buffer .= ' type="' . $strAttr['mime'] . '"';
			}
			if ($strAttr['defer'])
			{
				$buffer .= ' defer="defer"';
			}
			if ($strAttr['async'])
			{
				$buffer .= ' async="async"';
			}
			$buffer .= '></script>' . $newline;
		}
		
		//js 代码段解析
		
		foreach ($this->_script as $type => $content)
		{
			$buffer .= $tab . '<script type="' . $type . '">' . $newline;
		
			// This is for full XHTML support.
			if ($this->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . '<![CDATA[' . $newline;
			}
		
			$buffer .= $content . $newline;
		
			// See above note
			if ($this->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . ']]>' . $newline;
			}
			$buffer .= $tab . '</script>' . $newline;
		}
		
		return $buffer;
		
		
	}
	public function addScripts($url,$type="text/javascript",$defer=false,$async=false){
		$this->_scripts[$url]['mime']=$type;
		$this->_scripts[$url]['defer']=$defer;
		$this->_scripts[$url]['async']=$async;
		return $this;
		
	}
	public function addScriptCode($content,$type='text/javascript'){
		if(!isset($this->_script[strtolower($type)])){
			
			$this->_script[strtolower($type)]=$content;
		}else{
			//追加JS代码
			$this->_script[strtolower($type)] .="\15".$content;
		}
		
	}
	
	public function addStyles($url,$type='text/css',$media=null,$attribs=array()){
		
		$this->_styleSheets[$url]['mime']=$type;
		$this->_styleSheets[$url]['media']=$media;
		$this->_styleSheets[$url]['attribs']=$attribs;
		return $this;
	}

	
	public function addStyleCode($content,$type='text/css'){
		if(!isset($this->_style[$type])){
			
			$this->_style[strtolower($type)]=$content;
		}else{
			
			$this->_style[strtolower($type)] .= "\15".$content;
		}
		return $this;
	}
	public function getMetaTags($name,$httpEquiv=false){
		
		$result='';
		$name=strtolower($name);
		if($name=='description'){
			
			$result=getDescription();
		}else{
			
			
			if($httpEquiv==true){
				
				$result= $this->_metaTags['http_equiv'][$name];
			}else{
				$result=$this->_metaTags['standard'][$name];
			}
		}
		return $result;
	}
	public function setMetaTags($name,$content,$http_equiv=false,$sync=true){
		
		$name=strtolower($name);
		
		if($name=='description'){
			$this->setDescription($content);
		}else{
			
			if($http_equiv==true){
				
				$this->_metaTags['http-equiv'][$name]=$content;
				
				if($sync&&strtolower($name)=='content-type'){
					
					$this->setMimeEncoding($content);
				}
				
			}else{
				
				
				$this->_metaTags['standard'][$name]=$content;
			}
			
			
			
		}
		
	}
	public function setMimeEncoding($type='text/html',$sync=false){
		$this->_mime=$type;
		if($sync){
			
			$this->setMetaTags('content-type', $type,true,false);
		}
		
	}
	public function getMimeEncoding(){
		
		return $this->_mime;
	}
	public function getDescription(){
		
		return $this->description;
	}
	public function setDescription($content){
		
		$this->description=$content;
		return $this;
	}

	public function setLink($link){
		
		$this->_link=$link;
		return $this;
	}
	public function getLink(){
		
		return $this->_link;
	}
	public function setTab($tab){
		$this->_tab=$tab;
		return $this;
		
	}
	public function setCharset($charset) {
		
		$this->_charset = $charset;
		return $this;
	
	}
	public function getCharset(){
		
		return $this->_charset;
	}
	public function setLineEnd($style) {
		switch ($style) {
			case 'win' :
				$this->_linEnd = '\15\12';
				break;
			case 'unix' :
				$this->_linEnd = '\12';
				break;
			case 'mac' :
				$this->_linEnd = '\15';
				break;
			default :
				$this->_linEnd = $style;
		}
	}
	public static function getInstance($type='html',$attribute=array()) {
		
		if (empty ( self::$instance)) {
			
			$type=preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
			$path=dirname(__FILE__).'/'.$type.'/'.$type.'.php';
		
			$classname='Document'.strtoupper($type);
			if(!class_exists($classname)){
				
				if(file_exists($path)){
					
					require_once $path;
				}else{
					
					exit('documnent文档类型文件不存在');
				}
			}
			$instance_obj=new $classname($attribute);
			

			self::$instance = &$instance_obj;
			
			if($type!=null){
				$instance_obj->setType($type);
			}
			
			
		}
		
		return self::$instance;
	}
	
	public function setBuffer($string) {
		
		self::$buffer = $string;
	
	}
	public function getBuffer() {
		
		return self::$buffer;
	}
	
	public function loadRender($type){
		$classname='DocumentRender'.$type;
		
		if(!class_exists($classname)){
		
		}
		if(!class_exists($classname)){
			return null;
		}
		
		$renderobj=new $classname;
		
		return $renderobj;
	}
	public function render(){
		
		
		Response::setHeader('Content-Type', $this->_mime. '; charset='.$this->_charset);
		
		
	}
	
	public function parse($params=array()){
		
		return $this;
	}

}
?>