<?php
class RegistryFormatJSON extends RegistryFormat{
	
	
	
	
	/* (non-PHPdoc)
	 * @see RegistryFormat::stringToObject()
	 */
	public function stringToObject($data,$option=array()) {
		// TODO Auto-generated method stub
		return json_decode($data);
	}

	/* (non-PHPdoc)
	 * @see RegistryFormat::objecToString()
	 */
	public function objecToString($data,$option=array()) {
		// TODO Auto-generated method stub
		
		return json_encode($data);
		
	}

	
	
	
	
}