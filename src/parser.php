<?php
namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

function parse($сontent, $dataType)
{
    switch ($dataType) {
        case 'yml':
            $data = Yaml::parse($сontent);
            break;
        case 'json':
            $data = json_decode($сontent, true);
            break;
        default:
            $e = new \Exception("Extention '{$dataType}' is not supported\n");
            throw $e;
            break;
    }
    return $data;
}
