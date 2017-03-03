<?php
namespace wapmorgan\PhpCodeFixer;

function password_hash_salt_option(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    // if no extra options passed
    if (!isset($tree[1]))
        return false;
    $data = PhpCodeFixer::delimByComma($tree[1]);
    $data = PhpCodeFixer::trimSpaces($data);

    // searching for 'salt' option
    foreach ($data as $array_element) {
        if ($array_element[0][0] == T_CONSTANT_ENCAPSED_STRING) {
            $element_key = trim($array_element[0][1], '\'"');
            if ($element_key == 'salt')
                return true;
        }
    }

    return false;
}
