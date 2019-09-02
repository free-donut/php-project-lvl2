<?php
namespace GenDiff\Parse;

use Symfony\Component\Yaml\Yaml;

function parser($filePath)
{
    if (file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);
        $extention = pathinfo($filePath, PATHINFO_EXTENSION);
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
    } else {
        $e = new \Exception("'{$filePath}' is not exist\n");
        throw $e;
    }
}
