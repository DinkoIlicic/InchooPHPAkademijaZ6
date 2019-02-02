<?php

define('BP', __DIR__ . '/');

error_reporting(E_ALL);
ini_set('display_errors',1);

$includePaths = implode(PATH_SEPARATOR, [
    BP . 'app/model',
    BP . 'app/controller',
    BP . 'app/model/entity'
]);

set_include_path($includePaths);
spl_autoload_register(function ($class)
{
    $class = strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';
    $file = stream_resolve_include_path($class);
    if($file){
        include $file;
        return true;
    }
    return false;

});

App::start();