<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 8.0
 * @param array $usage_tokens
 * @param string $function
 * @return bool|string
 */
function optional_parameter_before_required(array $usage_tokens, $function) {
    if (count($usage_tokens) === 1)
        return false;

    $tree = PhpCodeFixer::makeFunctionCallTree($usage_tokens);
    if (!isset($tree[0])) // when first argument in call is something like `split(($a + $b))`
        return false;

    $data = PhpCodeFixer::divideByComma($tree[0]);
    if(count($data) === 0){ // One param, check irrelevant;
        return false;
    }
    $data = PhpCodeFixer::trimSpaces($data);

    foreach ($data as $argI => $arg) {
        if (isset($arg[0]) && is_array($arg[0]) && $arg[0][0] === T_VARIABLE) {
            if (isset($arg[1]) && is_string($arg[1]) && $arg[1] == '=') {
                return
                'Declaring optional before required is deprecated. '
                .'Problem is "'.$arg[0][1].'".'
                .' Try using function '.$function
                .'($Requried, '.$arg[0][1].' = Default)';
            }
        }
    }
    return false;
}
