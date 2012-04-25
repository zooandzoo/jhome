<?php 

function color($value)
	{
		$value = trim($value);

		if (empty($value))
		{
			$value = '#000000';
			return true;
		}

		if ($value[0] != '#')
		{
			return false;
		}

		$value = ltrim($value, '#');

		if (!((strlen($value) == 6 || strlen($value) == 3) && ctype_xdigit($value)))
		{
			return false;
		}

		$value = '#' . $value;

		return true;
	}
