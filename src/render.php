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
    if ($node["type"] === "array") {
      # code...
      $newIndent = $indent . INDENT;
      $key = $node['key'];
      $nested = render($node['child'], $newIndent);
      $newAcc = $acc . INDENT . $key .": {\n" . $nested .INDENT . "}\n";
      return $newAcc;
    } if ($node["type"] === "unchanged") {
      # code...
      $key = $node['key'];     
      if (is_array($node['beforeValue'])) {
        # code...
        $value = arrayToString($node['beforeValue']);
        $newAcc = $acc . $indent . INDENT .  $key . ": $value\n";
        return $newAcc;
      }
      $value = boolToString($node['beforeValue']);
      $newAcc = $acc . $indent . INDENT . $key . ": $value\n";
      return $newAcc;
    } if ($node['type'] == 'changed') {
      # code...
      $key = $node['key'];
      $beforeValue = boolToString($node['beforeValue']);
      $afterValue = boolToString($node['afterValue']);
      $newAcc = $acc . $indent . ADD . $key . ": $beforeValue\n" .$indent . DELETE . $key . ": $afterValue\n";
      return $newAcc; 
    } if ($node['type'] == 'deleted') {
      $key = $node['key'];
      if (is_array($node['beforeValue'])) {
        $deletedElem = arrayToString($node['beforeValue'], INDENT);
        $newAcc = $acc . $indent . DELETE. $key . ": {\n" . $indent . INDENT. $deletedElem . $indent . INDENT . "}\n";
        return $newAcc;
      }
      $deletedElem = boolToString($node['beforeValue']);
      $newAcc = $acc . $indent . DELETE . $key . ": $deletedElem\n";
      return $newAcc;
    } if ($node['type'] == 'added') {
      $key = $node['key'];
      if (is_array($node['afterValue'])) {
        $addedElem = arrayToString($node['afterValue'], INDENT);
        $newAcc = $acc . $indent . ADD . $key . ": {\n" . $indent . INDENT . $addedElem . $indent . INDENT . "}\n";
        return $newAcc;
      }
      $addedElem = boolToString($node['afterValue']);
      $newAcc = $acc . $indent . ADD . $key . ": $addedElem\n";
      return $newAcc;
    }
  }, '');

  return  $reduceArray;
}
