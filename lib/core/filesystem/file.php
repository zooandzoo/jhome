<?php
kimport('core.filesystem.path');
class Kfile{
	
	
	
	
	/**
	 * 读取文件内容
	 * @param string $filename  文件名称
 	 * @param string $inculde_path 是否在PHP.INI设置的INCLUDE_PATH中搜索
	 * @param unknown_type $amount 要读取的字节数
	 * @param unknown_type $chunksize 分块每次读取多少字节
	 * @param unknown_type $offset 开始位置
	 */
	public static function read($filename,$inculde_path=FALSE,$length=0,$chunksize=8912,$offset=0){
		$data='';
		if($length&&$chunksize>$length){
			$chunksize=$length;
		}
		if(false===$filehandle=fopen($filename, 'rb')){
			exit('打开文件失败，没有找到'.$filename.'文件');
		}
		fseek($filehandle, $offset);
		
		//文件大小小于系统默认的2G
		if($size=@filesize($filename)){
			if($size>$length){
				$data =fread($filehandle, $length);
			}else{
				$data=fread($handle, $size);
			}
		}else{
		//文件大小大于系统默认的2G则分块读取
		  while (!feof($filehandle)&&(!$length||strlen($data)<$length)){
		  	$data .= fread($filehandle, $chunksize);
		  }
		}
		
		
	}
	

	/**
	 * @param string $full_path 完整路径包括文件名
	 * @param mixed $data 写入数据
	 * @return boolean
	 */
	public static function write($full_path,$data){
		//返回文件目录部分
		$path=dirname($full_path);
		$path=Path::clean($path);
		
		$rs=is_int(file_put_contents($full_path, $data))?true:false;
		return $rs;
	}
	/**
	 * 删除文件
	 * @param string $file 文件完整绝对路径
	 */
	public static function delete($file){
		
		$files=array();
		if(is_array($file)){
			$files=$file;
		}else{
			
			$files[]=$file;
		}
		foreach($files as $value){
			
			if(!@unlink($value)){
				
				exit('删除'.$value.'失败');
			}
			
		}
		
		return true;
		
	}
	
	
	/**
	 * 移动文件
	 * @param src $src 源文件
	 * @param src $target 目标文件
	 * 
	 */
	public static function move($src,$target){
		
		if(!is_readable($src)){
			
			exit('文件不可读');
		}
		
		if(!@rename($src, $target)){
			
			exit('文件移动失败');
		}
		return true;
		
	}
	/**
	 * 
	 * 复制文件
	 * @param string $src	
	 * @param string $target
	 */
	public static function copy($src,$target){
		
		if(!is_readable($src)){
				
			exit('文件不可读');
		}
		
		if(!@copy($src, $target)){
				
			exit('文件复制失败');
		}
		return true;
		
		
		
	}
	
	
	
	
	
}