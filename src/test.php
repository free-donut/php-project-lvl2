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

