<?php
namespace GenDiff\Diff;

use \Funct\Collection;
use function GenDiff\Parse\parser;
use function GenDiff\Render\render;
use function GenDiff\Builder\buildAST;



function boolToString($value)
{
  if (is_bool($value)) {
    return ($value === true) ? 'true' : 'false';
  }
  return $value;
}

/*function getDiff($pathToFile1, $pathToFile2)
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
*/

function getDiff($pathToFile1, $pathToFile2)
{

  $array1 = parser($pathToFile1);
  $array2 = parser($pathToFile2);
  $ast = buildAST($array1, $array2);
  $render = render($ast, '');
  return "{\n$render}\n";
}