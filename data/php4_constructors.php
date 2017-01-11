<?php
namespace wapmorgan\PhpCodeFixer;

function php4_constructors($class_name, $function_name) {
    if (strcasecmp($class_name, $function_name) === 0)
        return true;
    return false;
}
