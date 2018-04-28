<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 5.3
 * @param array $usage_tokens
 * @param string $function
 * @return bool
 */
function call_with_passing_by_reference(array $usage_tokens, $function) {
    if (count($usage_tokens) === 1)
        return false;
    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    if (!isset($tree[0])) // when first argument in call is something like `split(($a + $b))`
        return false;
    $data = PhpCodeFixer::divideByComma($tree[0]);
    $data = PhpCodeFixer::trimSpaces($data);

    foreach ($data as $argI => $arg) {
        if (isset($arg[0]) && $arg[0] === '&') {
            if (isset($arg[1]) && is_array($arg[1]) && $arg[1][0] === T_VARIABLE) {
                return 'Call with passing by reference is deprecated. Problem is "&'.$arg[1][1].'"';
            }
        }
    }

    return false;
}
