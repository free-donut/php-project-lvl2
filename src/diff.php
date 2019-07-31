<?php
namespace GenDiff\Diff;

use \Funct\Collection;

function boolToString($value)
{
  if (is_bool($value)) {
    return ($value === true) ? 'true' : 'false';
  }
  return $value;
}

function getArray($filePath)
{
  if(file_exists($filePath)) {
    $fileContent = file_get_contents($filePath);
    $array = json_decode($fileContent, true);
  } else {
    $array = [];
  }
  return $array;
}

function getDiff($pathToFile1, $pathToFile2)
{ 

  $array1 = getArray($pathToFile1);
  $array2 = getArray($pathToFile2);

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

  //echo ("{\n$reduceArray}\n");
  return ("{\n$reduceArray}\n");
}
