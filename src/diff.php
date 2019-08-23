<?php
namespace GenDiff\Diff;

use \Funct\Collection;
use function GenDiff\Parse\parser;
use function GenDiff\Render\render;
use function GenDiff\Builder\buildAST;
use function GenDiff\Builder\buildMapAST;
use function GenDiff\Plain\getPlain;


function boolToString($value)
{
  if (is_bool($value)) {
    return ($value === true) ? 'true' : 'false';
  }
  return $value;
}


function getDiff($pathToFile1, $pathToFile2, $format)
{

  $beforeData = parser($pathToFile1);
  $afterData = parser($pathToFile2);

  $ast = buildMapAST($beforeData, $afterData);
  if ($format == 'json') {
  	$diff = render($ast, '');
  } if ($format == 'plain') {
  	$diff = getPlain($ast);
  } if ($format == 'pretty') {
  	$diff = json_encode($ast);
  }
  //сделать исключение если не поддерживаемый формат
  return "{\n$diff}\n";
}