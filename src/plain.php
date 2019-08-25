<?php

namespace GenDiff\Plain;

function boolToString($value)
{
  if (is_bool($value)) {
    return ($value === true) ? 'true' : 'false';
  }
  return $value;
}

function convertValue ($value) {
	if (is_array($value)) {
		return 'complex value';
	} else {
		return boolToString($value);
	}
}

function getPlain($ast, $parent = '')
{
	$view = array_reduce($ast, function ($acc, $node) use ($parent) {
	    if ($parent == '') {
	    	$property = $node["key"];
	    }else {
	    	$property = $parent . "." . $node["key"];
	    }
	    $key = $node['key'];
	    $type = $node["type"];
	    switch ($type) {
	    	case 'unchanged':
	    		return $acc;
	    		break;
	    	case 'array':
	    		$elem = getPlain($node["child"], $property);
	    		break;
	    	case 'changed':
		    	$beforeValue = convertValue($node['beforeValue']);
			    $afterValue = convertValue($node['afterValue']);
			    $elem = "Property '$property' was changed. From '$beforeValue' to '$afterValue'\n";
	    		break;
	    	case 'deleted':
	    		$elem = "Property '$property' was removed\n";
	    		break;
	    	case 'added':
	    		$afterValue = convertValue($node['afterValue']);
		    	$elem = "Property '$property' was added with value: '$afterValue'\n";
	    		break;
	    }
	    $newAcc = $acc . $elem;
	    return $newAcc;
	}, '');
	return $view;
}

