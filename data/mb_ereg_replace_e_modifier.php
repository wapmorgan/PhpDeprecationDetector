<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.1
 * @param array $usage_tokens
 * @return bool|string
 */
function mb_ereg_replace_e_modifier(array $usage_tokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    $data = PhpCodeFixer::trimSpaces($data);

    if (count($data) == 4 && $data[3][0][0] == T_CONSTANT_ENCAPSED_STRING) {
        $string = trim($data[3][0][1], '\'"');
        if (strpos($string, 'e') !== false) {
            return 'Usage of "e" modifier in mb_ereg_replace is deprecated: "'.$data[3][0][1].'"';
        }
    }

    return false;
}
