<?php
namespace GenDiff\Diff;

use \Funct\Collection;
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
        $newAcc = "$acc    $key: $array1[$key]\n";
        return $newAcc;
      } else {
        $newAcc = "$acc  + $key: $array2[$key]\n  - $key: $array1[$key]\n";
        return $newAcc;
      }      
    } if (isset($array1[$key])) {
      $newAcc = "$acc  - $key: $array1[$key]\n";
      return $newAcc;
    } else {
      $newAcc = "$acc  + $key: $array2[$key]\n";
      return $newAcc;
    }
  }, '');

  //echo ("{\n$reduceArray}\n");
  return ("{\n$reduceArray}\n");
}
