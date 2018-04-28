<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.2
 * @param array $usage_tokens
 * @return bool
 */
function assert_on_string(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    if ($data[0][0][0] == T_CONSTANT_ENCAPSED_STRING)
        return 'You should avoid using string code: "'.$data[0][0][1].'"';
    return false;
}
