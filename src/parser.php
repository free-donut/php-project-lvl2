<?php
namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse($сontent, $extention)
{
    switch ($extention) {
        case 'yml':
            $data = Yaml::parse($сontent);
            break;
        case 'json':
            $data = json_decode($сontent, true);
            break;
        default:
            $e = new \Exception("Extention '{$extention}' is not supported\n");
            throw $e;
            break;
    }
    return $data;
}
