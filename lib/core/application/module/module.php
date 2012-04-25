<?php
class Module{
	

	/**
	 * 
	 * 通过module名称来查找一个module
	 * @param string $name
	 * @return array modul属性数组
	 */
	public static function getModule($name){
		
		$result=null;
		$modules=self::_load();
		$total=count($modules);
		for ($i=0;$i<$total;$i++){
			$module_name=preg_replace('/mod_/', '', $modules[$i]['module']);
			if($module_name==$name){
				
				$result=$modules[$i];
				break;
			}
			
		}
		
		return $result;
		
	}
	/**
	 * 通过位置来获取指定位置的MODULES
	 * @param string $postion 位置名称
	 * @return array $result 返回当前位置的所有modules
	 */
	public static function getModules($postion){
		$result=array();
		$modules_array_list=self::_load();
		
		for ($i=0;$i<count($modules_array_list);$i++){
			
			
			if($modules_array_list[$i]['postion']==$postion){
				
				$result[]= $modules_array_list[$i];
			
			}
			
		}
		
		return $result;
	}
	
	/**
	 * 从数据库读取所有模块
	 * @return array
	 */
	public static function _load(){
		//改进需要写缓存
		$dbo=Factory::getDbo();
		$modules_list=$dbo->query_list("SELECT * FROM ".$dbo->table("modules"));
		
		return $modules_list;
	}
	
	/**
	 * 包含模块入口并渲染
	 * @param array $params
	 * 
	 * @return string
	 */
	public static function renderModule($params){
		$buffer='';
		$module=preg_replace('/[^A-Z0-9_\.-]/i', '', $params['module']);
		$path=CURRENT_APP_PATH.'/modules/'.$module.'/'.$module.'.php';
		if(file_exists($path)){
			
			$content='';
			ob_start();
			include $path;
			$buffer=ob_get_contents().$content;
			ob_end_clean();
			
		}
		
		return $buffer;
		
	}
	/**
	 * 通过smarty输出模块
	 * @param array $params		从数据库获取的模块参数
	 */
	public static function display($params){
		
		$attr_array=unserialize($params['params']);

		$layout=empty($attr_array['layout'])?'default':$attr_array['layout'];
		$tmpl_obj=Factory::getTemplateObject();
		$tmpl_obj->compile_dir=CURRENT_APP_PATH.'/data/modules/'.$params['module'].'/'.$params['module'].'/tmpl';
		$tmpl_obj->cache_dir=CURRENT_APP_PATH.'/data/modules/'.$params['module'].'/'.$params['module'].'/tmpl';
		$tmpl_obj->template_dir=CURRENT_APP_PATH.'/modules/'.$params['module'].'/tmpl';
		$tmpl=self::getLayoutPath($params['module'],$attr_array['layout']);
	
		$tmpl_obj->display($tmpl);

	}
	public static  function getLayoutPath($module_name,$layout='default'){

		$tmpl_path=CURRENT_APP_PATH.'/modules/'.$module_name.'/tmpl/'.$layout.'.html';
		
		if(file_exists($tmpl_path)){
			
			return $tmpl_path;
		}else{
			
			exit('模块模板不存在');
		}
		
	}
	

}