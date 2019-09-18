<?php
namespace Differ\formatters\plain;

use function Differ\formatters\pretty\boolToString;
use \Funct\Collection;

function convertValue($value)
{
    if (is_array($value)) {
        return 'complex value';
    } else {
        return boolToString($value);
    }
}

function getPlain($ast, $parent = '')
{
    $view = array_reduce($ast, function ($acc, $node) use ($parent) {
        $property = ($parent === '') ? $node["key"] : $parent . "." . $node["key"];
        switch ($node["type"]) {
            case 'array':
                $acc[] = getPlain($node["child"], $property);
                break;
            case 'changed':
                $beforeValue = convertValue($node['beforeValue']);
                $afterValue = convertValue($node['afterValue']);
                $acc[] = "Property '$property' was changed. From '$beforeValue' to '$afterValue'";
                break;
            case 'deleted':
                $acc[] = "Property '$property' was removed";
                break;
            case 'added':
                $afterValue = convertValue($node['afterValue']);
                $acc[] = "Property '$property' was added with value: '$afterValue'";
                break;
        }
        return $acc;
    }, []);
    return implode(PHP_EOL, $view);
}
