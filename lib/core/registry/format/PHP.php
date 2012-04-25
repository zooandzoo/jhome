<?php
class RegistryFormatPHP extends RegistryFormat{
	
	public function objecToString($object,$params=array()){
		
		$varstring='';
		$vars=get_object_vars($object);
		
		foreach ($var as $key=>$value){
			
			if(is_scalar($value)){
				
				$varstring.='\tpublic $'. $key . '" =" ' .addcslashes($value, '\\\'').'";\n';
				
			}elseif(is_array($value)||is_object($value)){
				
				$varstring.='\tpublic $'. $key . '" = " ';
				$varstring.=$this->getArrayString($value);
			}
			
		}
		
		$string='<?php\nclass '.get_class($object).'{\n';
		
		$string .=$varstring;
		$string .= "}";
		
		return $string;
	}
	
	public  function stringToObject($data){
		
	}
	protected function getArrayString($array){
		$i=0;
		$array_string='array(';
		foreach($array as $key => $value){
			
			$array_string .= ($i)?', ':'';
			$array_string .= '"'.$key.' "=> ';
			if(is_array($value)||is_object($value)){
				
				$array_string.=$this->getArrayString((array)$value);
			}else{
				
				$array_string .= '"'. addslashes($value).'"';
				
			}
			
			$i++;
		}
		$array_string .= ')';
		return $array_string;
	}
	
	
}