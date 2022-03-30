<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * @test 7.4, 8.0
 * @param array $usageTokens
 * @return void|string
 */
function implode_param_order_check(array $usageTokens)
{
    $tree = PhpCodeFixer::makeFunctionCallTree($usageTokens);
    $data = PhpCodeFixer::divideByComma($tree[0]);
    if(count($data) === 0){ //implode() one param;
        return;
    }

    $trimmedData = PhpCodeFixer::trimSpaces($data[0][0]);
    $firstParameter = $trimmedData[1];
    if(strpos($firstParameter, '$') !== false){
        return 'Passing the separator after the array is no longer supported. You should use implode(string $glue , array $pieces)';
    }
}
