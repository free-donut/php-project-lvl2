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
      $newAcc = $indent . "$acc$key: $value\n";
      return $newAcc;
    }, '');
    return $reduceArray;
}

function render($ast, $indent = '')
{
  $reduceArray = array_reduce($ast, function ($acc, $node) use ($indent) {
    $key = $node['key'];
    $type = $node["type"];
    if ($type === "array") {
      $newIndent = $indent . INDENT;
      $nested = render($node['child'], $newIndent);
      $newAcc = $acc . INDENT . $key .": {\n" . $nested .INDENT . "}\n";
    } if ($type === "unchanged") {
        if (is_array($node['beforeValue'])) {
          $value = arrayToString($node['beforeValue']);
        } else {
          $value = boolToString($node['beforeValue']);
        }
      $newAcc = $acc . $indent . INDENT . $key . ": $value\n";
    } if ($type === 'changed') {
      $beforeValue = boolToString($node['beforeValue']);
      $afterValue = boolToString($node['afterValue']);
      $newAcc = $acc . $indent . ADD . $key . ": $afterValue\n" .$indent . DELETE . $key . ": $beforeValue\n";
    } if ($type === 'deleted') {
        if (is_array($node['beforeValue'])) {
          $deletedElem = arrayToString($node['beforeValue'], INDENT);
          $newAcc = $acc . $indent . DELETE. $key . ": {\n" . $indent . INDENT. $deletedElem . $indent . INDENT . "}\n";
        } else {
          $deletedElem = boolToString($node['beforeValue']);
          $newAcc = $acc . $indent . DELETE . $key . ": $deletedElem\n";
        }
    } if ($type === 'added') {
        if (is_array($node['afterValue'])) {
          $addedElem = arrayToString($node['afterValue'], INDENT);
          $newAcc = $acc . $indent . ADD . $key . ": {\n" . $indent . INDENT . $addedElem . $indent . INDENT . "}\n";
        } else {
          $addedElem = boolToString($node['afterValue']);
          $newAcc = $acc . $indent . ADD . $key . ": $addedElem\n";
        }
    }
  return $newAcc;
  }, '');

  return  $reduceArray;
}
