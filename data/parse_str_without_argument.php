<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.2
 * @param array $usage_tokens
 * @return bool
 */
function parse_str_without_argument(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    if (count($data) < 2)
        return 'Pass a variable as second argument to parse_str() call';
    return false;
}