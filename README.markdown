Redis Fulltext Search Engine for chinese
===========================================

It's a new project,mostly used for replace the use fulltext search in mysql for chinese website.
Any questions? Mail me : wenhui@ncu.me

NOTICE : this project is under highly development,can't not be used in productive situation!!!!!


Requirements:
==============
  This search engine is written in PHP and is base on [redis](http://redis.io "redis home page")
  and [scws](https://github.com/hightman/scws "a simple chinese word spliter").So you need to install 
  a capable runtime environment at first:

  You need a webserver(like apache or nginx),a php module(php5_module for apache,
  or php-fpm for nginx),a php extension for redis operations(here we use [phpredis](https://github.com/owlient/phpredis "phpredis on github")) and the scws extension(github project:[scws](https://github.com/hightman/scws "a simple chinese word spliter")).

Usage:
===============
  see example/index.php
