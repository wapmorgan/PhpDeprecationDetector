<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.2
 * @param array $usageTokens
 * @return bool|string
 */
function assert_on_string(array $usageTokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usageTokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);

    if (!is_array($data[0][0]) || $data[0][0][0] != T_CONSTANT_ENCAPSED_STRING)
        return false;

    return 'You should avoid using string code: "'.$data[0][0][1].'"';
}
