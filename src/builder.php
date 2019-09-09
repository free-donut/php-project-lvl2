<?php
namespace GenDiff\Builder;

use \Funct\Collection;

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

function buildAST($beforeData, $afterData)
{
    $unionKeys = Collection\union(array_keys($beforeData), array_keys($afterData));
    
    $ast = array_map(function ($key) use ($beforeData, $afterData) {
        if (isset($beforeData[$key]) && isset($afterData[$key])) {
            if ($beforeData[$key] == $afterData[$key]) {
                $node = buildNode('unchanged', $key, $beforeData[$key], $afterData[$key], '');
                return $node;
            } elseif (is_array($beforeData[$key]) && is_array($afterData[$key])) {
                $children = buildAST($beforeData[$key], $afterData[$key]);
                $node = buildNode('array', $key, null, null, $children);
            } else {
                $node = buildNode('changed', $key, $beforeData[$key], $afterData[$key], '');
            }
        } elseif (isset($beforeData[$key]) && !isset($afterData[$key])) {
            $node = buildNode('deleted', $key, $beforeData[$key], null, '');
        } else {
            $node = buildNode('added', $key, null, $afterData[$key], '');
        }
        return $node;
    }, $unionKeys);

    return array_values($ast);
}
