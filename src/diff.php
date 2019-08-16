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


function getDiff($pathToFile1, $pathToFile2)
{

  $array1 = parser($pathToFile1);
  $array2 = parser($pathToFile2);
  $ast = buildAST($array1, $array2);
  $render = render($ast, '');
  return "{\n$render}\n";
}