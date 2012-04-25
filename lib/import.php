<?php
define('LIB', dirname(__FILE__));//框架lib目录绝对路径D:\xxx\lib
define('ROOT', realpath(WEB_ROOT));
define('FRAME_CORE', LIB.'/core');
//计算入口文件路径
$current_fullpath=$_SERVER['SCRIPT_FILENAME'];
$current_path=preg_replace('/[\/\\\\]\w+\.php$/', '', $current_fullpath).'/';
define('CURRENT_APP_PATH', realpath($current_path.APP_PATH));
define('THEMS',CURRENT_APP_PATH.'/templates');
define('CACHEBASE', CURRENT_APP_PATH.'/cache');



//导入smarty
require_once LIB.'/smarty/Smarty.php';
require_once LIB.'/classloader.php';

ClassLoader::regautoload();


ClassLoader::register('Module', FRAME_CORE.'/application/module/module.php');
ClassLoader::register('Kfile', FRAME_CORE.'/filesystem/file.php');

kimport('core.base.base');
kimport('core.application.application');
kimport('core.factory');
kimport('core.error.error');
kimport('core.environment.uri');
kimport('core.environment.request');
kimport('core.filter.input');
kimport('core.utilities.arrayhelper');


kimport('core.application.component.component');
kimport('core.application.controller.controller');
kimport('core.application.model.model');
kimport('core.application.view.view');
kimport('core.environment.response');
kimport('core.environment.uri');
kimport('core.environment.request');

?>