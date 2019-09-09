<?php
namespace GenDiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parse($fileContent, $extention)
{
    switch ($extention) {
        case 'yml':
            $value = Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
            $data = get_object_vars($value);
            break;
        case 'json':
            $data = json_decode($fileContent, true);
            break;
        default:
            $e = new \Exception("Extention '{$extention}' is not supported\n");
            throw $e;
            break;
    }
    return $data;
}
