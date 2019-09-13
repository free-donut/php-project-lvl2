<?php
namespace Differ\Formatters\Pretty;
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
        $newAcc = $acc . $indent . INDENT . INDENT . "$key: $value\n";
        return $newAcc;
    }, '');
    return $reduceArray;
}

function convertValue($value, $indent)
{
    if (is_array($value)) {
        $elem = arrayToString($value, $indent);
        return "{\n" . $elem . $indent . INDENT . "}";
    } else {
        return boolToString($value);
    }
}

function getArrayItem($child, $key, $indent)
{
    $newIndent = $indent . INDENT;
    $nested = renderPretty($child, $newIndent);
    $item = INDENT . $key . ": {\n" . $nested . INDENT . "}\n";
    return $item;
}

function getUnchangedItem($value, $key, $indent)
{
    $elem = convertValue($value, $indent);
    $item = $indent . INDENT . $key . ": $elem\n";
    return $item;
}

function getChangedItem($beforeValue, $afterValue, $key, $indent)
{
    $beforeElem = boolToString($beforeValue);
    $afterElem = boolToString($afterValue);
    $item = $indent . ADD . $key . ": $afterElem\n" . $indent . DELETE . $key . ": $beforeElem\n";
    return $item;
}

function getDeletedItem($value, $key, $indent)
{
    $deletedElem = convertValue($value, $indent);
    $item = $indent . DELETE . $key . ": $deletedElem\n";
    return $item;
}

function getAddedItem($value, $key, $indent)
{
    $addedElem = convertValue($value, $indent);
    $item = $indent . ADD . $key . ": $addedElem\n";
    return $item;
}

function renderPretty($ast, $indent = '')
{
    $view = array_reduce($ast, function ($acc, $node) use ($indent) {
        $key = $node['key'];
        switch ($node["type"]) {
            case 'array':
                $item = getArrayItem($node['child'], $key, $indent);
                break;
            case 'unchanged':
                $item = getUnchangedItem($node['beforeValue'], $key, $indent);
                break;
            case 'changed':
                $item = getChangedItem($node['beforeValue'], $node['afterValue'], $key, $indent);
                break;
            case 'deleted':
                $item = getDeletedItem($node['beforeValue'], $key, $indent);
                break;
            case 'added':
                $item = getAddedItem($node['afterValue'], $key, $indent);
                break;
        }
        $newAcc = $acc . $item;
        return $newAcc;
    }, '');
    return  $view;
}

function getPretty($ast)
{
    $render = renderPretty($ast, '');
    return  "{\n$render}\n";
}
