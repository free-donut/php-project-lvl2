<?php
namespace GenDiff\Test;

use \Funct\Collection;
use Symfony\Component\Yaml\Yaml;

function run()
{
	$fileContent = file_get_contents('tests/testFiles/before.yml');
	$value = Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
	//var_dump(get_object_vars($value));
	print_r(pathinfo('tests/testFiles/before.yml', PATHINFO_EXTENSION));
}

function getData($filePath)
{
  if(file_exists($filePath)) {
    $fileContent = file_get_contents($filePath);
    $array = json_decode($fileContent, true);
  } else {
    $array = [];
  }
  return $array;
}


function parse($filePath)
{
  if(file_exists($filePath)) {
  	$fileContent = file_get_contents($filePath);
  	$extention = pathinfo($filePath, PATHINFO_EXTENSION);
  	if ($extention == 'yml') {
      $value = Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
  	  $array = get_object_vars($value);
  	} if ($extention == 'json') {
	  $array = json_decode($fileContent, true);
    } else {
      $array = [];
    }
  }
  return $array;
}
 /*
function test() {
$code = <<<'EOC'
<?php
$var = 42;
EOC;
var_dump(ast\parse_code($code, $version=50));	
}
*/
function boolToString($value)
{
  if (is_bool($value)) {
    return ($value === true) ? 'true' : 'false';
  }
  return $value;
}

function buildNode($type, $key, $beforeValue, $afterValue, $child = null)
{
  $node = [];
  $node['type'] = $type;
  $node['key'] = $key;
  $node['beforeValue'] = $beforeValue;
  $node['afterValue'] = $afterValue;
  $node['child'] = $child;
  return $node;
}
//$unionKeys = Collection\union(array_keys($array1), array_keys($array2));

//var_dump($unionArray);

function buildAST($array1, $array2)
{
  $unionKeys = Collection\union(array_keys($array1), array_keys($array2));

  $reduceArray = array_reduce($unionKeys, function ($acc, $key) use ($array1, $array2) {
  	//если существуют оба значения
    if (isset($array1[$key]) && isset($array2[$key])) {
    	//если оба значения - массивы
      if(is_array($array1[$key]) && is_array($array2[$key])) {
      	//вернуть АСД массивов
      	$child = buildAST($array1[$key], $array2[$key]);
      	$elem = buildNode('array', $key, null, null, $child);
      	//$elem = AST($array1[$key], $array2[$key]);
      	$acc[] = $elem; 
        return $acc;
        // если оба значения равны
      } if ($array1[$key] == $array2[$key]) {
      	//вернуть АСД с типом "без изменений"
      	$elem = buildNode('unchanged', $key, $array1[$key], $array2[$key], '');
        $acc[] = $elem;
        return $acc;
      } else {
      	//венуть АСД с типом "изменен"
      	$elem = buildNode('changed', $key, $array1[$key], $array2[$key], '');
        $acc[] = $elem;
        return $acc;
      }
    // если существует только первое значение
    } if (isset($array1[$key])) {

        //если внутри значения массив
        /*if (is_array($array1[$key])) {
          # code...
          $child = buildAST('type', $array1[$key]);
          $elem = buildNode('deletedArray', $key, null, null, $child);
          $acc[] = $elem;
          return $acc;          
        }*/

      //вернуть АСД с типом "удален"
      $elem = buildNode('deleted', $key, $array1[$key], null, '');
      $acc[] = $elem;
      return $acc;
    //если существует только второе значение
    } else {
      //вернуть АСД со значением "добавен"
      $elem = buildNode('added', $key, null, $array2[$key], '');
      $acc[] = $elem;
      return $acc;
    }
  }, []);
  return $reduceArray;  
}

function getDiff($pathToFile1, $pathToFile2)
{ 

  $array1 = parser($pathToFile1);
  $array2 = parser($pathToFile2);

  $unionArray = Collection\union($array1, $array2);
  $unionKeys = array_keys(($unionArray));

  $reduceArray = array_reduce($unionKeys, function ($acc, $key) use ($array1, $array2) {
    if (isset($array1[$key]) && isset($array2[$key])) {
      if ($array1[$key] == $array2[$key]) {
        $elem = boolToString($array1[$key]);
        $newAcc = "$acc    $key: $elem\n";
        return $newAcc;
      } else {
        $elem1 = boolToString($array1[$key]);
        $elem2 = boolToString($array2[$key]);
        $newAcc = "$acc  + $key: $elem2\n  - $key: $elem1\n";
        return $newAcc;
      }      
    } if (isset($array1[$key])) {
      $elem = boolToString($array1[$key]);
      $newAcc = "$acc  - $key: $elem\n";
      return $newAcc;
    } else {
      $elem = boolToString($array2[$key]);
      $newAcc = "$acc  + $key: $elem\n";
      return $newAcc;
    }
  }, '');

  return ("{\n$reduceArray}\n");
}


function arrayToString($array)
{
    $reduceArray = array_reduce(array_keys($array), function ($acc, $key) use ($array) {
      $value = boolToString($array[$key]);
      $newAcc = "$acc$key: $value\n";
      return $newAcc;
    }, '');
    return $reduceArray;
}

function render($ast, $indent)
{
	//$indent = '  ';
	$reduceArray = array_reduce($ast, function ($acc, $node) use ($indent) {
		if ($node["type"] === "array") {
			# code...
			$newIndent = "$indent$indent";
      $key = $node['key'];
			$nested = render($node['child'], $newIndent);
			$newAcc = "$acc$indent$key: {\n$nested}\n";
			return $newAcc;
		} if ($node["type"] === "unchanged") {
			# code...
      $key = $node['key'];     
      if (is_array($node['beforeValue'])) {
        # code...
        $value = arrayToString($node['beforeValue']);
        $newAcc = "$acc$indent  $key: $value\n";
        return $newAcc;
      }
			$value = boolToString($node['beforeValue']);
			$newAcc = "$acc$indent  $key: $value\n";
			return $newAcc;
		} if ($node['type'] == 'changed') {
			# code...
			$key = $node['key'];
			$beforeValue = arrayToString($node['beforeValue']);
			$afterValue = arrayToString($node['afterValue']);
			$newAcc = "$acc$indent+ $key: $beforeValue\n$indent- $key: $afterValue\n";
			return $newAcc;	
		} if ($node['type'] == 'deleted') {

      $key = $node['key'];
      if (is_array($node['beforeValue'])) {
        # code...
        $deletedElem = arrayToString($node['beforeValue']);
        $newAcc = "$acc$indent- $key: {\n$indent$indent$indent$indent$deletedElem$indent$indent}\n";
        return $newAcc;
      }
      $deletedElem = boolToString($node['beforeValue']);
      $newAcc = "$acc$indent- $key: $deletedElem$indent\n";
      return $newAcc;
		} if ($node['type'] == 'added') {

      $key = $node['key'];
      if (is_array($node['afterValue'])) {
        $addedElem = arrayToString($node['afterValue']);
        $newAcc = "$acc$indent+ $key: {\n$indent$indent$indent$indent$addedElem$indent$indent}\n";
        return $newAcc;
      }
      $addedElem = boolToString($node['afterValue']);
      $newAcc = "$acc$indent+ $key: $addedElem\n";
      return $newAcc;
		}
	}, '');

	return $reduceArray;
}
