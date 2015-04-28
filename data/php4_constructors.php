<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * Handles T_CLASS token.
 */
// function php4_constructors(array &$tokens, $i, $token) {
function php4_constructors($class_name, $function_name) {
    if (strcasecmp($class_name, $function_name) === 0)
        return true;
    return false;
}