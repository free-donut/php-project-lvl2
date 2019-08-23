<?php
namespace GenDiff\Parse;

use \Funct\Collection;
use Symfony\Component\Yaml\Yaml;

function parser($filePath)
{
  if(file_exists($filePath)) {
  	$fileContent = file_get_contents($filePath);
  	$extention = pathinfo($filePath, PATHINFO_EXTENSION);
  	if ($extention == 'yml') {
      $value = Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
  	  $array = get_object_vars($value);
  	} if ($extention == 'json') {
      $array = json_decode($fileContent, true);
    }
    //сделать исключение для формата файла
  } else {
    /*
    $e = new \Exception("Extention '{$extention}' is not supported");
    throw $e;
    */
    $array = [];
  }
  return $array;
}