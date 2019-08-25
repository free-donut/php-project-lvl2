<?php
namespace GenDiff\Render;

use \Funct\Collection;
use Symfony\Component\Yaml\Yaml;
const ADD = '  + ';
const DELETE = '  - ';
const INDENT = '    ';

function boolToString($value)
{
  if (is_bool($value)) {
    return ($value === true) ? 'true' : 'false';
  }
  return $value;
}

function arrayToString($array, $indent = '')
{
    $reduceArray = array_reduce(array_keys($array), function ($acc, $key) use ($array, $indent) {
      $value = boolToString($array[$key]);
      $newAcc = $acc . $indent . INDENT. INDENT . "$key: $value\n";
      return $newAcc;
    }, '');
    return $reduceArray;
}

function arrayOrString($value, $indent) {
  if (is_array($value)) {
    $elem = arrayToString($value, $indent);
    return "{\n" . $elem . $indent . INDENT . "}";
  } else {
    return boolToString($value);
  }
}
function render($ast, $indent = '')
{
  $view = array_reduce($ast, function ($acc, $node) use ($indent) {
    $key = $node['key'];
    $type = $node["type"];
    switch ($type) {
      case 'array':
        $newIndent = $indent . INDENT;
        $nested = render($node['child'], $newIndent);
        $newAcc = $acc . INDENT . $key .": {\n" . $nested .INDENT . "}\n";        
        break;
      case 'unchanged':
        $value = arrayOrString($node['beforeValue'], $indent);
        $newAcc = $acc . $indent . INDENT . $key . ": $value\n";
        break; 
      case 'changed':
      //node с типом 'изменен' может содержать только строки
        $beforeValue = boolToString($node['beforeValue']);
        $afterValue = boolToString($node['afterValue']);
        $newAcc = $acc . $indent . ADD . $key . ": $afterValue\n" .$indent . DELETE . $key . ": $beforeValue\n";        
        break; 
      case 'deleted':
        $deletedElem = arrayOrString($node['beforeValue'], $indent);
        $newAcc = $acc . $indent . DELETE . $key . ": $deletedElem\n";
        break; 
      case 'added':
        $addedElem = arrayOrString($node['afterValue'], $indent);
        $newAcc = $acc . $indent . ADD . $key . ": $addedElem\n";
        break; 
    }
    return $newAcc;
  }, '');
  return  $view;
}
