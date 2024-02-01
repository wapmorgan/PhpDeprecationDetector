<?php
namespace wapmorgan\PhpCodeFixer;

/**
 *
 * @test 7.4
 * @param array $usageTokens
 * @return bool|string
 */
function curly_braces_dep(array $usageTokens){
    if (count($usageTokens) === 0)
        return false;

    return 'Array and string offset access using curly braces is deprecated. '
        .'Use ' . $usageTokens[0] . '[$idx] instead of ' . $usageTokens[0]
        . '{$idx}.';
}
