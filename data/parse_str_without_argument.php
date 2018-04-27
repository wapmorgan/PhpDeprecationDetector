<?php
namespace wapmorgan\PhpCodeFixer;

function parse_str_without_argument(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    if (count($data) < 2)
        return true;
    return false;
}