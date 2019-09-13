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
function renderPretty($ast, $indent = '')
{
    $view = array_reduce($ast, function ($acc, $node) use ($indent) {
        $key = $node['key'];
        switch ($node["type"]) {
            case 'array':
                $newIndent = $indent . INDENT;
                $nested = renderPretty($node['child'], $newIndent);
                $newAcc = $acc . INDENT . $key . ": {\n" . $nested . INDENT . "}\n";
                break;
            case 'unchanged':
                $elem = convertValue($node['beforeValue'], $indent);
                $newAcc = $acc . $indent . INDENT . $key . ": $elem\n";
                break;
            case 'changed':
                $beforeElem = boolToString($node['beforeValue']);
                $afterElem = boolToString($node['afterValue']);
                $newAcc = $acc . $indent . ADD . $key . ": $afterElem\n" . $indent . DELETE . $key . ": $beforeElem\n";
                break;
            case 'deleted':
                $deletedElem = convertValue($node['beforeValue'], $indent);
                $newAcc = $acc . $indent . DELETE . $key . ": $deletedElem\n";
                break;
            case 'added':
                $addedElem = convertValue($node['afterValue'], $indent);
                $newAcc = $acc . $indent . ADD . $key . ": $addedElem\n";
                break;
        }
        return $newAcc;
    }, '');
    return  $view;
}

function getPretty($ast)
{
    $render = renderPretty($ast, '');
    return  "{\n$render}\n";
}
