<?php
namespace GenDiff\Builder;

use \Funct\Collection;
use Symfony\Component\Yaml\Yaml;

function buildNode($type, $key, $beforeValue, $afterValue, $child = null)
{
  $node = [
    'type' => $type,
    'key' => $key,
    'beforeValue' => $beforeValue,
    'afterValue' => $afterValue,
    'child' => $child
  ];
  return $node;
}

function buildMapAST($beforeData, $afterData)
{
  $unionKeys = Collection\union(array_keys($beforeData), array_keys($afterData));

  $ast = array_map(function ($key) use ($beforeData, $afterData) {
    //если существуют оба значения
    if (isset($beforeData[$key]) && isset($afterData[$key])) {
      if ($beforeData[$key] == $afterData[$key]) {
        //вернуть АСД с типом "без изменений"
        $node = buildNode('unchanged', $key, $beforeData[$key], $afterData[$key], '');
        return $node;     
      //если оба значения - массивы
      } if (is_array($beforeData[$key]) && is_array($afterData[$key])) {
        //вернуть вложенный node
        $child = buildAST($beforeData[$key], $afterData[$key]);
        $node = buildNode('array', $key, null, null, $child);
      } else {
        //венуть АСД с типом "изменен"
        $node = buildNode('changed', $key, $beforeData[$key], $afterData[$key], '');
      }
      return $node;    
    // если существует только первое значение
    } if (isset($beforeData[$key])) {
      //вернуть АСД с типом "удален"
      $node = buildNode('deleted', $key, $beforeData[$key], null, '');
    //если существует только второе значение
    } else {
      //вернуть АСД со значением "добавен"
      $node = buildNode('added', $key, null, $afterData[$key], '');
    }
      return $node;    
  }, $unionKeys);
  
  return $ast;
}