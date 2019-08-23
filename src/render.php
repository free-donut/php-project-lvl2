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
    if ($type === "array") {
      $newIndent = $indent . INDENT;
      $nested = render($node['child'], $newIndent);
      $newAcc = $acc . INDENT . $key .": {\n" . $nested .INDENT . "}\n";
    } if ($type === "unchanged") {
      $value = arrayOrString($node['beforeValue'], $indent);
      $newAcc = $acc . $indent . INDENT . $key . ": $value\n";
    //node с типом изменен может содержать только строки
    } if ($type === 'changed') {
      $beforeValue = boolToString($node['beforeValue']);
      $afterValue = boolToString($node['afterValue']);
      $newAcc = $acc . $indent . ADD . $key . ": $afterValue\n" .$indent . DELETE . $key . ": $beforeValue\n";
    } if ($type === 'deleted') {
        $deletedElem = arrayOrString($node['beforeValue'], $indent);
        $newAcc = $acc . $indent . DELETE . $key . ": $deletedElem\n";  
    } if ($type === 'added') {
        $addedElem = arrayOrString($node['afterValue'], $indent);
        $newAcc = $acc . $indent . ADD . $key . ": $addedElem\n";       
    }
  return $newAcc;
  }, '');
  return  $view;
}
