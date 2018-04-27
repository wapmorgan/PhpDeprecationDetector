<?php
namespace wapmorgan\PhpCodeFixer;

function assert_on_string(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    if ($data[0][0][0] == T_CONSTANT_ENCAPSED_STRING)
        return true;
    return false;
}
