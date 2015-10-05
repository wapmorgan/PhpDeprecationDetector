<?php
namespace wapmorgan\PhpCodeFixer;

function in_array_column($haystack, $needle, $column, $strict = false) {
    if ($strict) {
        foreach ($haystack as $k => $elem) {
            if ($elem[$column] === $needle)
                return true;
        }
        return false;
    } else {
        foreach ($haystack as $k => $elem) {
            if ($elem[$column] == $needle)
                return true;
        }
        return false;
    }
}

function array_search_column($haystack, $needle, $column, $strict = false) {
    if ($strict) {
        foreach ($haystack as $k => $elem) {
            if ($elem[$column] === $needle)
                return $k;
        }
        return false;
    } else {
        foreach ($haystack as $k => $elem) {
            if ($elem[$column] == $needle)
                return $k;
        }
        return false;
    }
}

function array_filter_by_column($source, $needle, $column) {
    $filtered = array();
    foreach ($source as $elem) {
        if ($elem[$column] == $needle)
            $filtered[] = $elem;
    }
    return $filtered;
}

class PhpCodeFixer {
    static public $fileSizeLimit;

    static public function checkDir($dir, IssuesBank $issues) {
        echo 'Scanning '.$dir.' ...'.PHP_EOL;
        self::checkDirInternal($dir, $issues);
    }

    static protected function checkDirInternal($dir, IssuesBank $issues) {
        foreach (glob($dir.'/*') as $file) {
            if (is_dir($file))
                self::checkDirInternal($file, $issues);
            else if (is_file($file) && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), array('php', 'php5', 'phtml'))) {
                self::checkFile($file, $issues);
            }
        }
    }

    static public function checkFile($file, IssuesBank $issues) {
        if (self::$fileSizeLimit !== null && filesize($file) > self::$fileSizeLimit) {
            fwrite(STDOUT, 'Skipping file '.$file.' due to file size limit.'.PHP_EOL);
            return;
        }
        $tokens = token_get_all(file_get_contents($file));

        // cut off heredoc, comments
        while (in_array_column($tokens, T_START_HEREDOC, 0)) {
            $start = array_search_column($tokens, T_START_HEREDOC, 0);
            $end = array_search_column($tokens, T_END_HEREDOC, 0);
            array_splice($tokens, $start, ($end - $start + 1));
        }

        // find for deprecated functions
        $deprecated_functions = $issues->getAll('functions');
        $used_functions = array_filter_by_column($tokens, T_STRING, 0);
        foreach ($used_functions as $used_function) {
            if (isset($deprecated_functions[$used_function[1]])) {
                $function = $deprecated_functions[$used_function[1]];
                fwrite(STDERR, '['.$function[1].'] Function '.$used_function[1].' is deprecated in file '.$file.'['.$used_function[2].']. ');
                if ($function[0] != $used_function[1])
                    fwrite(STDERR, 'Consider using '.$function[0].' instead.');
                fwrite(STDERR, PHP_EOL);
            }
        }

        // find for deprecated ini settings
        $deprecated_ini_settings = $issues->getAll('ini_settings');
        foreach ($tokens as $i => $token) {
            if ($token[0] == T_STRING && in_array($token[1], array('ini_alter', 'ini_set', 'ini_​get', 'ini_restore'))) {
                // syntax structure check
                if ($tokens[$i+1] == '(' && is_array($tokens[$i+2]) && $tokens[$i+2][0] == T_CONSTANT_ENCAPSED_STRING) {
                    $ini_setting = $tokens[$i+2]; // ('ini_setting'
                    $ini_setting[1] = trim($ini_setting[1], '\'"');
                    if (isset($deprecated_ini_settings[$ini_setting[1]])) {
                        $deprecated_setting = $deprecated_ini_settings[$ini_setting[1]];
                        fwrite(STDERR, '['.$deprecated_setting[1].'] Ini setting '.$ini_setting[1].' is deprecated in file '.$file.'['.$ini_setting[2].']. ');
                        if ($deprecated_setting[0] != $ini_setting[1])
                            fwrite(STDERR, 'Consider using '.$deprecated_setting[0].' instead.');
                        fwrite(STDERR, PHP_EOL);
                    }
                }
            }
        }

        // find for deprecated functions usage
        $deprecated_functions_usage = $issues->getAll('functions_usage');
        foreach ($tokens as $i => $token) {
            if ($token[0] != T_STRING)
                continue;
            if (!isset($deprecated_functions_usage[$token[1]]))
                continue;
            // get func arguments
            $function = array($token);
            $k = $i+2;
            $braces = 1;
            while ($braces > 0 && isset($tokens[$k])) {
                $function[] = $tokens[$k];
                if ($tokens[$k] == ')') {/*var_dump($tokens[$k]);*/ $braces--;}
                else if ($tokens[$k] == '(') {/*var_dump($tokens[$k]);*/ $braces++; }
                // var_dump($braces);
                $k++;
            }
            //$function[] = $tokens[$k];
            $fixer = ltrim($deprecated_functions_usage[$token[1]][0], '@');
            require_once dirname(dirname(__FILE__)).'/data/'.$fixer.'.php';
            $fixer = __NAMESPACE__.'\\'.$fixer;
            $result = $fixer($function);
            if ($result)
                fwrite(STDERR, '['.$deprecated_functions_usage[$token[1]][1].'] Function '.$token[1].' usage is deprecated ('.$deprecated_functions_usage[$token[1]][0].') in file '.$file.'['.$token[2].'].'.PHP_EOL);
        }

        // find for deprecated variables
        $deprecated_varibales = $issues->getAll('variables');
        $used_variables = array_filter_by_column($tokens, T_VARIABLE, 0);
        foreach ($used_variables as $used_variable) {
            if (isset($deprecated_varibales[$used_variable[1]])) {
                $variable = $deprecated_varibales[$used_variable[1]];
                fwrite(STDERR, '['.$variable[1].'] Variable '.$used_variable[1].' is deprecated in file '.$file.'['.$used_variable[2].']. ');
                if ($variable[0] != $used_variable[1])
                        fwrite(STDERR, 'Consider using '.$variable[0].' instead.');
                    fwrite(STDERR, PHP_EOL);
            }
        }

        // find for methods naming deprecations
        $methods_naming = $issues->getAll('methods_naming');
        if (!empty($methods_naming)) {
            while (in_array_column($tokens, T_CLASS, 0)) {
                $total = count($tokens);
                $i = array_search_column($tokens, T_CLASS, 0);
                $class_start = $i;
                $class_name = $tokens[$i+2][1];
                $braces = 1;
                $i += 5;
                while (($braces > 0) && (($i+1) <= $total)) {
                    if ($tokens[$i] == '{') {
                        $braces++;
                        /*echo '++';*/
                    } else if ($tokens[$i] == '}') {
                        $braces--;
                        /*echo '--';*/
                    } else if (is_array($tokens[$i]) && $tokens[$i][0] == T_FUNCTION && is_array($tokens[$i+2])) {
                        $function_name = $tokens[$i+2][1];
                        foreach ($methods_naming as $methods_naming_checker) {
                            $checker = ltrim($methods_naming_checker[0], '@');
                            require_once dirname(dirname(__FILE__)).'/data/'.$checker.'.php';
                            $checker = __NAMESPACE__.'\\'.$checker;
                            $result = $checker($class_name, $function_name);
                            if ($result) {
                                fwrite(STDERR, '['.$methods_naming_checker[1].'] Method name "'.$function_name.'" in class "'.$class_name.'" is deprecated ('.$methods_naming_checker[0].') in file '.$file.'['.$tokens[$i][2].'].'.PHP_EOL);
                            }

                        }
                    }
                    $i++;
                }
                array_splice($tokens, $class_start, $i - $class_start);
            }
        }
    }

    static public function makeFunctionCallTree(array $tokens) {
        $tree = array();
        $braces = 0;
        $i = 1;
        while (/*$braces > 0 &&*/ isset($tokens[$i])) {
            if ($tokens[$i] == '(') $braces++;
            else if ($tokens[$i] == ')') $braces--;
            else $tree[$braces][] = $tokens[$i];
            $i++;
        }
        return $tree;
    }

    static public function delimByComma(array $tokens) {
        $delimited = array();
        $comma = 0;
        foreach ($tokens as $token) {
            if ($token == ',') $comma++;
            else $delimited[$comma][] = $token;
        }
        return $delimited;
    }

    static public function trimSpaces(array $tokens) {
        $trimmed = array();
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] == T_WHITESPACE)
                continue;
            else
                $trimmed[] = $token;
        }
        return $trimmed;
    }
}