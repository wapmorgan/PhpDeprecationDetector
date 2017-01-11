<?php
namespace wapmorgan\PhpCodeFixer;

function mb_ereg_replace_e_modifier(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    $data = PhpCodeFixer::delimByComma($tree[1]);
    $data = PhpCodeFixer::trimSpaces($data);

    if (count($data) == 4 && $data[3][0][0] == T_CONSTANT_ENCAPSED_STRING) {
        $string = trim($data[3][0][1], '\'"');
        if (strpos($string, 'e') !== false) {
            return true;
        }
    }

    return false;
}
