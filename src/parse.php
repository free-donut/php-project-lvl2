<?php
namespace GenDiff\Parse;

use \Funct\Collection;
use Symfony\Component\Yaml\Yaml;

function parser($filePath)
{
  if(file_exists($filePath)) {
  	$fileContent = file_get_contents($filePath);
  	$extention = pathinfo($filePath, PATHINFO_EXTENSION);
    switch ($extention) {
      case 'yml':
        $value = Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
        $array = get_object_vars($value);
        break;
      case 'json':
        $array = json_decode($fileContent, true);
        break;      
      default:
        $e = new \Exception("Extention '{$extention}' is not supported\n");
        throw $e; 
        break;
    }
    return $array;  
  } else {    
    $e = new \Exception("'{$filePath}' is not exist\n");
    throw $e;
  }
}