# php-project-lvl2

[![Maintainability](https://api.codeclimate.com/v1/badges/08a99f23fa6675cc20ea/maintainability)](https://codeclimate.com/github/free-donut/php-project-lvl2/maintainability)

[![Test Coverage](https://api.codeclimate.com/v1/badges/08a99f23fa6675cc20ea/test_coverage)](https://codeclimate.com/github/free-donut/php-project-lvl2/test_coverage)

[![Build Status](https://travis-ci.org/free-donut/php-project-lvl2.svg?branch=master)](https://travis-ci.org/free-donut/php-project-lvl2)


## Description

This is a cli application to find differences in configuration files.


## Installation:

Via Composer

``` bash
$ composer global require free-donut/gendiff
```


## Usage:
``` bash
$ gendiff (-h|--help)
``` 
``` bash
$ gendiff [--format <fmt>] <firstFile> <secondFile>
```
  
Options:

  -h --help                     Show this screen
  
  --format <fmt>                Report format [default: pretty]
  
You can choose 'json' or 'plain' format. 'pretty' is the default format.


[![asciicast](https://asciinema.org/a/kNFdvQxDCIc4WJPsnbXBXshmH.svg)](https://asciinema.org/a/kNFdvQxDCIc4WJPsnbXBXshmH)


### parse yml

[![asciicast](https://asciinema.org/a/q6MzxdG39IeXdLtIDJrCT1V1q.svg)](https://asciinema.org/a/q6MzxdG39IeXdLtIDJrCT1V1q)


### parse nested json

[![asciicast](https://asciinema.org/a/bt2lphQrjL5GM6FOPpK7zp7Kd.svg)](https://asciinema.org/a/bt2lphQrjL5GM6FOPpK7zp7Kd)


### add plain format

[![asciicast](https://asciinema.org/a/ZVRccwSO4Yr2wmFSga6xN1Ws8.svg)](https://asciinema.org/a/ZVRccwSO4Yr2wmFSga6xN1Ws8)


### add pretty format

[![asciicast](https://asciinema.org/a/QZmqRbfYTEzeaZBUii5UlXNiv.svg)](https://asciinema.org/a/QZmqRbfYTEzeaZBUii5UlXNiv)
