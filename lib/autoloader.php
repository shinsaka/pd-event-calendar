<?php
spl_autoload_register(function ($class_name) {
    $file_path = dirname(__FILE__) . '/' . $class_name . '.php';
    if (file_exists($file_path)) {
        require_once($file_path);
    } else {
        $file_path = dirname(__FILE__) . '/' . $class_name . '.class.php';
        if (file_exists($file_path)) {
            require_once($file_path);
        }
    }
});
