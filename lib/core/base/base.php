<?php

class BaseObject {
	
	protected $_errors = array ();
	/**
	 *
	 * @author
	 *
	 *
	 * @param $property string
	 *       	 属性名
	 * @param $value mixed
	 *       	 设置属性的值
	 *       	
	 */
	public function set($property, $value = null) {
		
		$temp = isset ( $this->$property ) ? $this->$property : null;
		$this->$property = $value;
		return $property;
	
	}
	
	public function get($property, $def = null) {
		
		if (isset ( $this->$property )) {
			return $this->$property;
		}
		return $def;
	
	}
	
	function toString() {
		return $this->__toString ();
	}
	
	function __toString() {
		
		return get_class ( $this );
	}
	
	/**
	 * 会最后一个错误字符串
	 * @param int $i
	 * @return stirng
	 */
	function getError($i = null) {
		if ($i === null) {
			$error = end ( $this->_errors );
		} elseif (! array_key_exists ( $i, $this->_errors )) {
			return false;
		} else {
			$error = $this->_errors [$i];
		}
	
	}
	/**
	 * 返回错误数组
	 * @return array:
	 */
	function getErrors() {
		
		return $this->_errors;
	}
	
	function setError($error) {
		
		array_push ( $this->_errors, $error );
	}

}

?>