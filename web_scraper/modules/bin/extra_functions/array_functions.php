<?php
	/*
	function search_in_array($needle, $array, $key) {
	    $found = array();
	    foreach ($array as $key => $val) {
	        if ($val[$key] == $needle) {
	            array_push($found, $val[1]);
	        }
	    }
	    if (count($found) != 0)
	        return $found;
	    else
	        return null;
	}
	*/
	
	function get_most_occurances($array)
	{
		$count=array_count_values($array);//Counts the values in the array, returns associatve array
		arsort($count);//Sort it from highest to lowest
		$keys=array_keys($count);
		return $keys[0];
	}
	
	function array_keys_getall($array, &$all_key_names = array())
	{
		static $aux_check = 0;
		$aux_check++;
		$key_names = array_keys($array);
		addToArray($all_key_names, $key_names, "/^[0-9]+$/");
		foreach($key_names as $key)
		{
			if(gettype($array[$key]) == "array")
			{
				array_keys_getall($array[$key], $all_key_names);
			}
		}
		$aux_check--;
		if($aux_check == 0)
			return $all_key_names;
	}
	
	function trim_array($array, $removeNull = false)
	{
		$new_array = array();
		foreach($array as $row)
		{
			if(gettype($row) == "array")
			{
				array_push($new_array, trim_array($row, $removeNull));
			}
			else
			{
				if(gettype($row) == "string")
				{
					if(!(trim($row) == "" && $removeNull))
						array_push($new_array, trim($row));
				}
				else
				{
					array_push($new_array, $row);
				}
			}
		}
		return $new_array;
	}

	function addToArray(&$arrayInto, $arrayFrom, $checkForDuplicates = false, $checkpreg = NULL)
	{
		if(gettype($arrayFrom) != "array" && gettype($arrayFrom) != "object")
		{
			if($checkForDuplicates)
			{
				if(!checkIfExists($arrayInto, $arrayFrom))
				{
					array_push($arrayInto, $arrayFrom);
				}
			}
			else
			{
				array_push($arrayInto, $arrayFrom);
			}
		}
		else
		{
			if(!$checkForDuplicates == true)
			{
				foreach($arrayFrom as $item)
				{
					array_push($arrayInto, $item);
				}
			}
			else
			{
				if($checkpreg == null)
				{
					if((($item != NULL || $item != false) && $checkForDuplicates))
					{
						if(gettype($item) != "array" && gettype($item) != "object")
						{
							if(!checkIfExists($arrayInto, $item))
								array_push($arrayInto, $item);
						}
						else
						{
							array_push($arrayInto, $item);
						}
					}
				}
				else
				{
					foreach($arrayFrom as $item)
					{
						$secondaryCheck = true;
						if($checkpreg != null)
						{
							if(gettype($item) == "string")
							{
								$secondaryCheck = preg_match($checkpreg, $item) == 0;
							}
						}
						if((($item != NULL || $item != false) && $checkForDuplicates))
						{
							if(gettype($item) != "array" && gettype($item) != "object")
							{
								if(!checkIfExists($arrayInto, $item) && $secondaryCheck)
									array_push($arrayInto, $item);
							}
							else
							{
								array_push($arrayInto, $item);
							}
						}
					}
				}
			}
		}
		return $arrayInto;
	}

	function checkIfExists($array, $item, $data_sub_name = null, $sendBackArray = false)
	{
		if($array == false)
			return false;
		$returnValue = false;
		if($sendBackArray != false)
			$returnValue = array();
		$url_check = false;
		if(preg_match("/^http/", $item))
		{
			$item = trim($item, "/");
			$url_check = true;
		}
		foreach($array as $value)
		{
			if($data_sub_name != null)
			{
				$finalValue = null;
				if(preg_match("/^http/", $value[$data_sub_name]))
				{
					$finalValue = trim($value[$data_sub_name], "/");
				}
				else
				{
					$finalValue = $value[$data_sub_name];
				}
				if(strtolower($finalValue) == strtolower($item))
				{
					if($sendBackArray)
						array_push($returnValue, $value);
					else
						return TRUE;
						
				}
			}
			else
			{
				if(preg_match("/^http/", $value))
				{
					$value = trim($value, "/");
				}
				if(strtolower($value) == strtolower($item))
				{
					if($sendBackArray)
						array_push($returnValue, $value);
					else
						return TRUE;
				}
			}
		}
		if($returnValue)
		{
			if(count($returnValue) != 0)
				return $returnValue;
			else
				return false;
		}
		else
			return FALSE;
	}
	
	function array_similar($array1, $array2)
	{
		sort($array1);
		sort($array2);
		return $array1 == $array2;
	}
?>