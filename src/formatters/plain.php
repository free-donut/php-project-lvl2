<?php
namespace Differ\Formatters\Plain;

use function Differ\Formatters\Pretty\boolToString;

function convertValue($value)
{
    if (is_array($value)) {
        return 'complex value';
    } else {
        return boolToString($value);
    }
}

function getChangedMessage($beforeValue, $afterValue, $property)
{
    $beforeValueConverted = convertValue($beforeValue);
    $afterValueConverted = convertValue($afterValue);
    $message = "Property '$property' was changed. From '$beforeValueConverted' to '$afterValueConverted'\n";
    return $message;
}

function getDeletedMessage($property)
{
    $message = "Property '$property' was removed\n";
    return $message;
}

function getAddedMessage($afterValue, $property)
{
    $afterValueConverted = convertValue($afterValue);
    $message = "Property '$property' was added with value: '$afterValueConverted'\n";
    return $message;
}

function getPlain($ast, $parent = '')
{
    $view = array_reduce($ast, function ($acc, $node) use ($parent) {
        $property = ($parent === '') ? $node["key"] : $parent . "." . $node["key"];
        switch ($node["type"]) {
            case 'unchanged':
                $elem = '';
                break;
            case 'array':
                $elem = getPlain($node["child"], $property);
                break;
            case 'changed':
                $elem = getChangedMessage($node['beforeValue'], $node['afterValue'], $property);
                break;
            case 'deleted':
                $elem = getDeletedMessage($property);
                break;
            case 'added':
                $elem = getAddedMessage($node['afterValue'], $property);
                break;
        }
        $newAcc = $acc . $elem;
        return $newAcc;
    }, '');
    return $view;
}
