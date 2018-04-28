<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.0
 * @param string $class_name
 * @param string $function_name
 * @return bool
 */
function php4_constructors($class_name, $function_name) {
    if (strcasecmp($class_name, $function_name) === 0)
        return 'You should use __constructor() method instead of "'.$function_name.'"';
    return false;
}
