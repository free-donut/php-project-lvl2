<?php
namespace GenDiff\Diff;

use \Funct\Collection;
use function GenDiff\Parse\parser;
use function GenDiff\Render\render;
use function GenDiff\Builder\buildAST;
use function GenDiff\Plain\getPlain;


function getDiff($pathToFile1, $pathToFile2, $format)
{

  $beforeData = parser($pathToFile1);
  $afterData = parser($pathToFile2);

  $ast = buildAST($beforeData, $afterData);

  switch ($format) {
    case 'json':
      $diff = render($ast, '');
      break;
    case 'plain':
      $diff = getPlain($ast);
      break;
    case 'pretty':
      $diff = json_encode($ast);
      break;
    default:
      $e = new \Exception("Format '{$format}' is not supported\n");
      throw $e;
      break;
  }
  return "{\n$diff}\n";
}