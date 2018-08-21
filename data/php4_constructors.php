<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.0
 * @param $className
 * @param $methodName
 * @param array $methodAttributes
 * @param array $methods -- all methods of the given class
 * @return bool
 */
function php4_constructors($className, $methodName, array $methodAttributes, array $methods) {
    if (strcasecmp($className, $methodName) === 0 && !in_array('static', $methodAttributes, true) && !array_key_exists('__construct', $methods)) {
        return 'You should use __constructor() method instead of "'.$methodName.'"';
    }

    return false;
}
