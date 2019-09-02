<?php
$ast = [
                [
                    'type' => 'unchanged',
                    'key' => 'host',
                    'beforeValue' =>
                    'hexlet.io',
                    'afterValue' =>
                    'hexlet.io',
                    'child' => null
                ],
                [
                    'type' => 'changed',
                    'key' => 'timeout',
                    'beforeValue' => '50',
                    'afterValue' => '20',
                    'child' => null
                ],
                [
                    'type' => 'deleted',
                    'key' => 'proxy',
                    'beforeValue' => '123.234.53.22',
                    'afterValue' => null,
                    'child' => null
                ],
                [
                    'type' => 'added',
                    'key' => 'verbose',
                    'beforeValue' => null,
                    'afterValue' => true,
                    'child' => null]
        ];

return $ast;
