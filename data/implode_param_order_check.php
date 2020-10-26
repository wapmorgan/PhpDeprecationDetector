<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.3
 * @param array $usageTokens
 * @return bool|string
 */
function implode_param_order_check(array $usageTokens)
{
    $tree = PhpCodeFixer::makeFunctionCallTree($usageTokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    $data = PhpCodeFixer::trimSpaces($data[0]);

    if (!isset($tree[0][1])) {
        return;
    } //implode() one param;

    if ($data[0][0] != T_CONSTANT_ENCAPSED_STRING) {
        return;
    }

    return 'if use implode ( string $glue , array $pieces ) instead of implode(array $parts, string $glue). ';
}
