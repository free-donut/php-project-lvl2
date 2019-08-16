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

function buildNode($type, $key, $beforeValue, $afterValue, $child = null)
{
  $node = [
    'type' => $type,
    'key' => $key,
    'beforeValue' => $beforeValue,
    'afterValue' => $afterValue,
    'child' => $child
  ];
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
