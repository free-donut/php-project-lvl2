<?php

namespace GenDiff\Plain;

use function GenDiff\render\boolToString;

function stringOrArray ($value) {
	if (is_array($value)) {
		return 'complex value';
	} else {
		return boolToString($value);
	}
}

function getPlain($ast, $parent = '')
{
	$reduceArray = array_reduce($ast, function ($acc, $node) use ($parent) {
	    if ($parent == '') {
	    	$property = $node["key"];
	    }else {
	    	$property = $parent . "." . $node["key"];
	    }
	    $key = $node['key'];
	    $type = $node["type"];
	    if ($type == 'array') {
	    	$elem = getPlain($node["child"], $property);
	    	$acc[] = $elem;
	    	return $acc;
	    } if ($type == 'changed') {	    
	    	$beforeValue = stringOrArray($node['beforeValue']);
		    $afterValue = stringOrArray($node['afterValue']);	    	
	    	$acc[] = "Property '$property' was changed. From '$beforeValue' to '$afterValue'\n";
	    } if ($type =='deleted') {
			$acc[] = "Property '$property' was removed\n";
	    } if ($type == 'added') {
	    	$afterValue = stringOrArray($node['afterValue']);
	    	$acc[] = "Property '$property' was added with value: '$afterValue'\n";
	    }
	    return $acc;
	}, []);
	return implode('', $reduceArray);
}

