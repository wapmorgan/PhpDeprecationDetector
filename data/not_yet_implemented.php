<?php
namespace wapmorgan\PhpCodeFixer;

/**
 *
 * @test Any
 * @param array $usageTokens
 * @return bool|string
 */

function not_yet_implemented(array $usageTokens){
    if (count($usageTokens) === 0)
        return false;

    // Make these Vars so that in the future this could work for more than
    // just functions. See Issue #84
    $Pfx = 'Function';
    $Sfx = '()';
    return "$Pfx " . $usageTokens[0][1]
    . "$Sfx not yet implemented on this version of PHP.";
}
