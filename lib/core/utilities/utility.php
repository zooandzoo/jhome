<?php 

class Utility{
	
	public static  function parseAttributes($string){
		
		$attr=array();
		$return_attr=array();
		preg_match_all('/(\w+)[\s]?=[\s]?"([^"]*)/i', $string, $attr);
		
		if(is_array($attr)){
			
			for ($i=0;$i<count($attr);$i++){
				
				$return_attr[$attr[1][$i]]=$attr[2][$i];
				
			}
			
		}
		return $return_attr;
		
	}
	
	//获取在线IP
	public static 	function getonlineip($format=0) {
		global $_SGLOBAL;
	
		if(empty($_SGLOBAL['onlineip'])) {
			if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
				$onlineip = getenv('HTTP_CLIENT_IP');
			} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
				$onlineip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
				$onlineip = getenv('REMOTE_ADDR');
			} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
				$onlineip = $_SERVER['REMOTE_ADDR'];
			}
			preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
			$_SGLOBAL['onlineip'] = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
		}
		if($format) {
			$ips = explode('.', $_SGLOBAL['onlineip']);
			for($i=0;$i<3;$i++) {
				$ips[$i] = intval($ips[$i]);
			}
			return sprintf('%03d%03d%03d', $ips[0], $ips[1], $ips[2]);
		} else {
			return $_SGLOBAL['onlineip'];
		}
	}
	
}
