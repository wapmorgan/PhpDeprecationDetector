<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.0
 * @param array $usage_tokens
 * @return bool|string
 */
function password_hash_salt_option(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    // if no extra options passed
    if (!isset($tree[1]))
        return false;
    $data = PhpCodeFixer::divideByComma($tree[1]);
    $data = PhpCodeFixer::trimSpaces($data);

    // searching for 'salt' option
    foreach ($data as $array_element) {
        if ($array_element[0][0] == T_CONSTANT_ENCAPSED_STRING) {
            $element_key = trim($array_element[0][1], '\'"');
            if ($element_key == 'salt')
                return '"salt" option is not secure and deprecated now';
        }
    }

    return false;
}
