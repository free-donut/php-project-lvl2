<?php
namespace Differ\formatters\pretty;
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

function renderPretty($ast, $depth = 0)
{
    $view = array_map(function ($node) use ($depth) {
        $indent = str_repeat(INDENT, $depth);
        $key = $node['key'];
        switch ($node["type"]) {
            case 'array':
                $newDepth = $depth + 1;
                $nested = renderPretty($node['child'], $newDepth);
                $item = INDENT . $key . ": {" . $nested . INDENT . "}";
                break;
            case 'unchanged':
                $elem = convertValue($node['beforeValue'], $indent);
                $item = $indent . INDENT . $key . ": $elem";
                break;
            case 'changed':
                $beforeElem = boolToString($node['beforeValue']);
                $afterElem = boolToString($node['afterValue']);
                $item = $indent . ADD . $key . ": $afterElem\n" . $indent . DELETE . $key . ": $beforeElem";
                break;
            case 'deleted':
                $deletedElem = convertValue($node['beforeValue'], $indent);
                $item = $indent . DELETE . $key . ": $deletedElem";
                break;
            case 'added':
                $addedElem = convertValue($node['afterValue'], $indent);
                $item = $indent . ADD . $key . ": $addedElem";
                break;
        }
        return $item;
    }, $ast);
    $view = implode(PHP_EOL, $view);
    return "\n$view\n";
}

function getPretty($ast)
{
    $render = renderPretty($ast);
    return "{" . $render . "}";
}
