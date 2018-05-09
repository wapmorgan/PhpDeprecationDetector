<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.0
 * @param $className
 * @param $methodName
 * @param array $methodAttributes
 * @return bool
 */
function php4_constructors($className, $methodName, array $methodAttributes) {
    if (strcasecmp($className, $methodName) === 0 && !in_array('static', $methodAttributes, true))
        return 'You should use __constructor() method instead of "'.$methodName.'"';
    return false;
}
