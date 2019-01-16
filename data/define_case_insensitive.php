<?php
namespace wapmorgan\PhpCodeFixer;

/**
 *
 * @test 7.3
 * @param array $usageTokens
 * @return bool|string
 */
function define_case_insensitive(array $usageTokens)
{
    $tree = PhpCodeFixer::makeFunctionCallTree($usageTokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    $data = PhpCodeFixer::trimSpaces($data);
    if (count($data) < 3)
        return false;

    if (!is_array($data[2][0]) || $data[2][0][0] !== T_STRING)
        return false;

    if (strcasecmp($data[2][0][1], 'true') !== 0)
        return false;

    return 'Case-insensitive flag of define() is deprecated, use original constant name in your code';
}
