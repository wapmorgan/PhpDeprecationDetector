<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 5.5
 * @param array $usageTokens
 * @return bool|string
 */
function preg_replace_e_modifier(array $usageTokens) {
    $tree = PhpCodeFixer::makeFunctionCallTree($usageTokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    $data = PhpCodeFixer::trimSpaces($data[0]);

    // getting delimiter
    if ($data[0][0] != T_CONSTANT_ENCAPSED_STRING) {
        return false;
    }

    $string = substr($data[0][1], 1, -1);
    $delimiter = strtr($string{0}, '({[<', ')}]>');

    if ($data[count($data)-1][0] != T_CONSTANT_ENCAPSED_STRING) {
        return false;
    }

    $string = trim($data[count($data)-1][1], '\'"');
    $modifiers = strrchr($string, $delimiter);

    if (empty($modifiers) || strpos($modifiers, 'e') === false) {
        return false;
    }

    return 'Usage of "e" modifier in preg_replace is deprecated: "'.$string.'"';
}
