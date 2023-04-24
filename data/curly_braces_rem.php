<?php
namespace wapmorgan\PhpCodeFixer;

/**
 *
 * @test 8.0
 * @param array $usageTokens
 * @return bool|string
 */
function curly_braces_rem(array $usageTokens){
    if (count($usageTokens) === 0)
        return false;

    return $usageTokens[0] . '[$idx] instead of ' . $usageTokens[0]
        . '{$idx}.';
}
