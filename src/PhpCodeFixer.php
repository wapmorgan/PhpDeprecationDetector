<?php
namespace wapmorgan\PhpCodeFixer;

class PhpCodeFixer {

    /**
     * Version of analyzer
     */
    const VERSION = '2.0.29';

    /**
     * @var array
     */
    static public $availableTargets = ['5.3', '5.4', '5.5', '5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1'];

    /**
     * @var array Extensions of file to process.
     */
    static public $defaultFileExtensions = ['php', 'php5', 'phtml'];

    /**
     * @var string Target php version
     */
    protected $target;

    /**
     * @var string Initial php version
     */
    protected $after;

    /**
     * @var IssuesBank
     */
    protected $issuesBank;

    /**
     * @var string[]
     */
    protected $excludedChecks = [];

    /**
     * @var int|null
     */
    protected $fileMaxSizeLimit;

    /**
     * @var string[]
     */
    protected $excludeList = [];

    /**
     * @var string[]
     */
    protected $fileExtensions;

    /**
     * PhpCodeFixer constructor.
     */
    public function __construct()
    {
        $this->target = end(self::$availableTargets);
        $this->after = self::$availableTargets[0];
        $this->fileExtensions = self::$defaultFileExtensions;
    }

    /**
     * @param $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @param $target
     */
    public function setAfter($after)
    {
        $this->after = $after;
    }

    /**
     * @param array $excludeList
     */
    public function setExcludedChecks(array $excludeList)
    {
        $this->excludedChecks = $excludeList;
    }

    /**
     * @param array $exts
     */
    public function setFileExtensions(array $exts)
    {
        $this->fileExtensions = $exts;
    }

    /**
     * @param int|null $max_size
     */
    public function setFileSizeLimit($max_size)
    {
        $this->fileMaxSizeLimit = $max_size;
    }

    /**
     * @param array $excludeList
     */
    public function setExcludeList(array $excludeList)
    {
        $this->excludeList = $excludeList;
    }

    /**
     *
     */
    public function initializeIssuesBank()
    {
        // init issues bank
        $this->issuesBank = new IssuesBank();
        foreach (self::$availableTargets as $version) {
            if (version_compare($this->after, $version, '>'))
                continue;

            $version_issues = include dirname(dirname(__FILE__)).'/data/'.$version.'.php';

            foreach ($version_issues as $issues_type => $issues_list) {
                $this->issuesBank->import($version, $issues_type, $issues_list, $this->excludedChecks);
            }

            if ($version == $this->target)
                break;
        }
    }

    /**
     * @param IssuesBank $issues
     */
    public function setIssues(IssuesBank $issues)
    {
        $this->issuesBank = $issues;
    }

    /**
     * @param string $dir
     * @return Report
     */
    public function checkDir($dir) {
        $report = new Report('Folder '.$dir, $dir);
        $previous_error_handler = set_error_handler([$this, 'handleError']);
        $this->checkDirInternal($dir, $report);
        set_error_handler($previous_error_handler);
        return $report;
    }

    /**
     * @param string $dir
     * @param Report $report
     */
    protected function checkDirInternal($dir, Report $report) {
        if (in_array(strtolower(basename($dir)), $this->excludeList, true)) {
            $report->addInfo(Report::INFO_MESSAGE, 'Folder ' . $dir . ' skipped');
            return;
        }

        foreach (glob($dir.'/*') as $file) {
            $path_info = pathinfo($file);

            if (
                in_array(strtolower($path_info['filename']), $this->excludeList, true)
                || in_array(strtolower($path_info['basename']), $this->excludeList, true)
            ) {
                $location_type = (is_dir($file)) ? 'Folder' : 'File';
                $report->addInfo(Report::INFO_MESSAGE, $location_type . ' ' . $file . ' skipped');
                continue;
            }

            if (is_dir($file)) {
                $this->checkDirInternal($file, $report);
            }
            elseif (is_file($file) && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->fileExtensions, true)) {
                $this->checkFile($file, $report);
            }
        }
    }

    /**
     * @param string $file
     * @param Report|null $report
     * @return Report|bool
     */
    public function checkFile($file, Report $report = null) {
        if ($report === null) {
            $report = new Report('File ' . basename($file), dirname(realpath($file)));
            $previous_error_handler = set_error_handler([$this, 'handleError']);
        }

        if ($this->fileMaxSizeLimit !== null && filesize($file) > $this->fileMaxSizeLimit) {
            $report->addInfo(Report::INFO_MESSAGE, 'Skipping file '.$file.' due to file size limit.');
            return $report;
        }

        try {
            $tokens = token_get_all(file_get_contents($file));

            // cut off heredoc, comments
            while (in_array_column($tokens, T_START_HEREDOC, 0)) {
                $start = array_search_column($tokens, T_START_HEREDOC, 0);
                $end = array_search_column($tokens, T_END_HEREDOC, 0);
                array_splice($tokens, $start, ($end - $start + 1));
            }

            $this->analyzeFunctions($file, $report, $tokens);

            $this->analyzeConstants($file, $report, $tokens);

            $this->analyzeIniSettings($file, $report, $tokens);

            $this->analyzeFunctionsUsage($file, $report, $tokens);

            $this->analyzeVariables($file, $report, $tokens);

            $this->analyzeIdentifiers($file, $report, $tokens);

            $this->analyzeMethodsNaming($file, $report, $tokens);
        } catch (ParsingException $e) {
            $report->addInfo(Report::INFO_WARNING, $e->getMessage().' when parsing '.$file);
        }

        if (isset($previous_error_handler))
            set_error_handler($previous_error_handler);

        return $report;
    }

    /**
     * Creates a tokens hierarchy by () from plain list
     * @param array $tokens
     * @return array
     */
    static public function makeFunctionCallTree(array $tokens) {
        $tree = [];
        $braces_level = 0;
        $i = 1;

        while (/*$braces > 0 &&*/ isset($tokens[$i])) {
            if ($tokens[$i] === '(') $braces_level++;
            else if ($tokens[$i] === ')') $braces_level--;
            else $tree[$braces_level][] = $tokens[$i];
            $i++;
        }
        return $tree;
    }

    /**
     * Divide first level of tokens hierarchy by comma
     * @param array $tokens
     * @return array
     */
    static public function divideByComma(array $tokens) {
        $delimited = [];
        $comma = 0;
        foreach ($tokens as $token) {
            if ($token === ',') $comma++;
            else $delimited[$comma][] = $token;
        }
        return $delimited;
    }

    /**
     * Removes all T_WHITESPACE tokens from tokens hierarchy
     * @param array $tokens
     * @return array
     */
    static public function trimSpaces(array $tokens) {
        $trimmed = [];
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_WHITESPACE)
                    continue;
                else
                    $trimmed[] = self::trimSpaces($token);
            }
            else
                $trimmed[] = $token;
        }
        return $trimmed;
    }

    /**
     * Calls function-usage checker
     * @param string $checker
     * @param string $functionName
     * @param array $callTokens
     * @return boolean
     */
    protected static function callFunctionUsageChecker($checker, $functionName, array $callTokens)
    {
        require_once dirname(__DIR__).'/data/'.$checker.'.php';
        $checker = __NAMESPACE__ . '\\' . $checker;
        return $checker($callTokens, $functionName);
    }

    /**
     * @param array $tokens
     * @param int $class_pos
     * @param string|null $default
     * @return bool|string
     */
    private static function findClassNamespaceInTokens(array $tokens, $class_pos, $default = '') {
        $namespace_tokens = array_slice($tokens, 0, $class_pos - 1);
        $namespace_pos = array_search_column($namespace_tokens, T_NAMESPACE, 0);
        if (empty($namespace_pos)) {
            return $default;
        }
        $namespace_tokens = array_slice($namespace_tokens, $namespace_pos);
        $namespace = '';
        foreach ($namespace_tokens as $token) {
            if (is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {
                $namespace .= $token[1];
            }
            else if (in_array($token, [';', '{'])) {
                break;
            }
        }
        return (!empty($namespace)) ? $namespace : '';
    }

    /**
     * @param $currentFile
     * @param Report $report
     * @param array $tokens
     */
    protected function analyzeMethodsNaming($currentFile, Report $report, array &$tokens)
    {
        // find for methods naming deprecations
        $methods_naming = $this->issuesBank->getAll('methods_naming');
        if (!empty($methods_naming)) {
            $namespace = null;
            while (in_array_column($tokens, T_CLASS, 0)) {
                $total = count($tokens);
                $i = array_search_column($tokens, T_CLASS, 0);
                $class_start = $i;
                // check for ABC::class usage
                if (!is_array($tokens[$class_start - 1]) || ($tokens[$class_start - 1][1] !== '::' && $tokens[$class_start + 1] !== '(')) {
                    $namespace = self::findClassNamespaceInTokens($tokens, $class_start, $namespace);
                    $class_name = $tokens[$i + 2][1];
                    $methods = [];
                    $braces = 1;

                    while ($tokens[$i] !== '{') {
                        $i++;
                    }
                    $i++;

                    while (($braces > 0) && (($i + 1) <= $total)) {
                        if ($tokens[$i] === '{') {
                            $braces++;
                            /*echo '++';*/
                        } else if ($tokens[$i] === '}') {
                            $braces--;
                            /*echo '--';*/
                        } else if (is_array($tokens[$i]) && $tokens[$i][0] == T_FUNCTION && is_array($tokens[$i + 2])) {
                            $method_attributes = [];
                            $attributes_index = 2;
                            while (is_array($tokens[$i - $attributes_index])
                                && in_array($tokens[$i - $attributes_index][1], ['static', 'public', 'private', 'protected'], true)) {
                                $method_attributes[] = $tokens[$i - $attributes_index][1];
                                $attributes_index += 2;
                            }
                            $method_name = $tokens[$i + 2][1];
                            $methods[$method_name] = [
                                'line' => $tokens[$i][2],
                                'attributes' => $method_attributes
                            ];
                        }
                        $i++;
                    }
                    foreach ($methods as $method_name => $method_data) {
                        foreach ($methods_naming as $methods_naming_checker) {
                            $checker = ltrim($methods_naming_checker[0], '@');
                            require_once dirname(__DIR__) . '/data/' . $checker . '.php';
                            $checker = __NAMESPACE__ . '\\' . $checker;
                            $result = $checker($class_name, $method_name, $method_data['attributes'], $methods, $namespace);
                            if ($result !== false) {
                                $report->addIssue($methods_naming_checker[1], ReportIssue::CHANGED, ReportIssue::DEPRECATED_FEATURE,
                                    $method_name . ':' . $class_name . ' (' . $methods_naming_checker[0] . ')', $result, $currentFile, $method_data['line']);
                            }
                        }
                    }
                } else {
                    // ::class
                    $i++;
                }
                array_splice($tokens, 0, $i);
            }
        }
    }

    /**
     * @param $currentFile
     * @param Report $report
     * @param array $tokens
     */
    protected function analyzeIdentifiers($currentFile, Report $report, array &$tokens)
    {
        // oop reserved words
        // functions and constants are allowed to be used as identifiers
        $identifiers_prefixes = [T_CLASS, T_INTERFACE];
        if (defined('T_TRAIT')) $identifiers_prefixes[] = T_TRAIT;

        // find for reserved identifiers used as names
        $identifiers = $this->issuesBank->getAll('identifiers');

        foreach ($identifiers as $identifier => $identifierData)
        {
            if (strtolower($identifier) != $identifier) {
                $identifiers[strtolower($identifier)] = $identifiers[$identifier];
                unset($identifiers[$identifier]);
            }
        }

        if (!empty($identifiers)) {
            foreach ($tokens as $i => $token) {
                if (in_array($token[0], $identifiers_prefixes, true)) {
                    if (isset($tokens[$i + 2]) && is_array($tokens[$i + 2]) && $tokens[$i + 2][0] == T_STRING) {
                        $used_identifier = $tokens[$i + 2];
                        $used_identifier_word = strtolower($used_identifier[1]);
                        if (isset($identifiers[$used_identifier_word])) {
                            $identifier = $identifiers[$used_identifier_word];
                            $report->addIssue($identifier[1], ReportIssue::VIOLATION, ReportIssue::RESERVED_IDENTIFIER,
                                $used_identifier[1], null, $currentFile, $used_identifier[2]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $currentFile
     * @param Report $report
     * @param array $tokens
     */
    protected function analyzeVariables($currentFile, Report $report, array $tokens)
    {
        // find for deprecated variables
        $deprecated_varibales = $this->issuesBank->getAll('variables');
        $used_variables = array_filter_by_column($tokens, T_VARIABLE, 0);
        foreach ($used_variables as $used_variable) {
            if (isset($deprecated_varibales[$used_variable[1]])) {
                $variable = $deprecated_varibales[$used_variable[1]];
                $report->addIssue($variable[1], ReportIssue::REMOVED, ReportIssue::REMOVED_VARIABLE,
                    $used_variable[1], ($variable[0] != $used_variable[1] ? $variable[0] : null), $currentFile, $used_variable[2]);
            }
        }
    }

    /**
     * @param $currentFile
     * @param Report $report
     * @param array $tokens
     */
    protected function analyzeFunctionsUsage($currentFile, Report $report, array &$tokens)
    {
        // find for deprecated functions usage
        $deprecated_functions_usage = $this->issuesBank->getAll('functions_usage');

        /** @var array $global_deprecated_usage_checkers List of global checkers (for all function calls) */
        $global_deprecated_usage_checkers = [];
        foreach ($deprecated_functions_usage as $function => $function_usage_checker) {
            if (is_int($function)) {
                $global_deprecated_usage_checkers[] = $function_usage_checker;
                unset($deprecated_functions_usage[$function]);
            }
        }

        $function_declaration = false;
        $object_function_call = false;

        foreach ($tokens as $i => $token) {
            if ($token[0] == T_FUNCTION) {
                $function_declaration = true;
                continue;
            }
            if ($function_declaration === true) {
                if ($token === '{') {
                    $function_declaration = false;
                }
                continue;
            }
            // not a string: for sure not a function / method call
            if ($token[0] != T_STRING) {
                continue;
            }
            // not a function usage: method call
            // bug #36
            if ($tokens[$i - 1][0] === T_OBJECT_OPERATOR) {
                $object_function_call = true;
            }

            // skip whitespaces
            // bug #36
            while ($tokens[$i + 1][0] === T_WHITESPACE) {
                $i++;
            }

            // check if the next non-whitespace character is '('
            if ((!isset($tokens[$i + 1]) || $tokens[$i + 1] !== '(')) {
                continue;
            }

            if (!isset($deprecated_functions_usage[$token[1]]) && empty($global_deprecated_usage_checkers))
                continue;

            // get func arguments
            $functionTokens = [$token];
            $k = $i + 2;
            $braces = 1;
            while ($braces > 0 && isset($tokens[$k])) {
                if (count($functionTokens) > 1 || $tokens[$k] !== ')') {
                    if ($tokens[$k][0] !== T_WHITESPACE)
                        $functionTokens[] = $tokens[$k];
                }
                if ($tokens[$k] === ')') {/*var_dump($tokens[$k]);*/
                    $braces--;
                } else if ($tokens[$k] === '(') {/*var_dump($tokens[$k]);*/
                    $braces++;
                }
                // var_dump($braces);
                $k++;
            }
            //$function[] = $tokens[$k];

            if ($object_function_call) {
                $object_function_call = false;
                continue;
            }

            // checking exactly this function usage
            if (isset($deprecated_functions_usage[$token[1]])) {
                $result = self::callFunctionUsageChecker(ltrim($deprecated_functions_usage[$token[1]][0], '@'),
                    $token[1],
                    $functionTokens);

                if ($result) {
                    $report->addIssue($deprecated_functions_usage[$token[1]][1], ReportIssue::CHANGED, ReportIssue::DEPRECATED_FUNCTION_USAGE,
                        $token[1] . '() (' . $deprecated_functions_usage[$token[1]][0] . ')', is_string($result) ? $result : null, $currentFile, $token[2]);
                }
            }

            // checking global function usages
            if (!empty($global_deprecated_usage_checkers)) {
                foreach ($global_deprecated_usage_checkers as $global_function_usage_checker) {
                    $result = self::callFunctionUsageChecker(ltrim($global_function_usage_checker[0], '@'),
                        $token[1],
                        $functionTokens);
                    if ($result) {
                        $report->addIssue($global_function_usage_checker[1], ReportIssue::CHANGED, ReportIssue::DEPRECATED_FUNCTION_USAGE,
                            $token[1] . '() (' . $global_function_usage_checker[0] . ')', is_string($result) ? $result : null, $currentFile, $token[2]);
                    }
                }
            }
        }
    }

    /**
     * @param $currentFile
     * @param Report $report
     * @param array $tokens
     */
    protected function analyzeIniSettings($currentFile, Report $report, array $tokens)
    {
        // find for deprecated ini settings
        $deprecated_ini_settings = $this->issuesBank->getAll('ini_settings');
        foreach ($tokens as $i => $token) {
            if ($token[0] == T_STRING && in_array($token[1],
                    [
                        'ini_alter',
                        'ini_set',
                        // # ini_get() does not throw warnings
                        // 'ini_get',
                        'ini_restore',
                    ])) {
                // syntax structure check
                if ($tokens[$i + 1] == '(' && is_array($tokens[$i + 2]) && $tokens[$i + 2][0] == T_CONSTANT_ENCAPSED_STRING) {
                    $ini_setting = $tokens[$i + 2]; // ('ini_setting'
                    $ini_setting[1] = trim($ini_setting[1], '\'"');
                    if (isset($deprecated_ini_settings[$ini_setting[1]])) {
                        $deprecated_setting = $deprecated_ini_settings[$ini_setting[1]];
                        $report->addIssue($deprecated_setting[1], ReportIssue::REMOVED, ReportIssue::REMOVED_INI_SETTING,
                            $ini_setting[1], ($deprecated_setting[0] != $ini_setting[1] ? $deprecated_setting[0] : null), $currentFile, $ini_setting[2]);
                    }
                }
            }
        }
    }

    /**
     * @param $currentFile
     * @param Report $report
     * @param array $tokens
     */
    protected function analyzeConstants($currentFile, Report $report, array $tokens)
    {
        // find for deprecated constants
        $deprecated_constants = $this->issuesBank->getAll('constants');
        $used_constants = array_filter_by_column($tokens, T_STRING, 0, true);
        foreach ($used_constants as $used_constant_i => $used_constant) {
            if (isset($deprecated_constants[$used_constant[1]])) {
                $constant = $deprecated_constants[$used_constant[1]];
                $report->addIssue($constant[1], ReportIssue::REMOVED, ReportIssue::REMOVED_CONSTANT,
                    $used_constant[1], ($constant[0] != $used_constant[1] ? $constant[0] : null), $currentFile, $used_constant[2]);
            }
        }
    }

    /**
     * @param $currentFile
     * @param Report $report
     * @param array $tokens
     */
    protected function analyzeFunctions($currentFile, Report $report, array &$tokens)
    {
        // find for deprecated functions
        $deprecated_functions = $this->issuesBank->getAll('functions');
        $used_functions = array_filter_by_column($tokens, T_STRING, 0, true);
        foreach ($used_functions as $used_function_i => $used_function) {
            if (isset($deprecated_functions[$used_function[1]])) {
                $usage_offset = 0;
                // skip whitespaces
                // bug #36
                while ($tokens[$used_function_i + 1 + $usage_offset][0] === T_WHITESPACE) {
                    $usage_offset++;
                }

                // additional check for "(" after this token
                if (!isset($tokens[$used_function_i + $usage_offset + 1]) || $tokens[$used_function_i + $usage_offset + 1] !== '(')
                    continue;
                // additional check for lack of "->" and "::" before this token
                if (isset($tokens[$used_function_i - 1])
                    && is_array($tokens[$used_function_i - 1])
                    && in_array($tokens[$used_function_i - 1][0], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true))
                    continue;
                // additional check for lack of "function" before this token
                if (isset($tokens[$used_function_i - 2]) && is_array($tokens[$used_function_i - 2]) && $tokens[$used_function_i - 2][0] === T_FUNCTION)
                    continue;

                $function = $deprecated_functions[$used_function[1]];
                $report->addIssue($function[1], ReportIssue::REMOVED, ReportIssue::REMOVED_FUNCTION,
                    $used_function[1], ($function[0] != $used_function[1] ? $function[0] : null), $currentFile, $used_function[2]);
            }
        }
    }

    /**
     * @param int $errorNumber
     * @param string $errorMessage
     * @param string $errorFile
     * @param int $errorLine
     *
     * @throws \Exception
     */
    public function handleError($errorNumber, $errorMessage, $errorFile,
        $errorLine)
    {
        throw new ParsingException($errorMessage.' in file '.$errorFile.':'.$errorLine, $errorNumber);
    }
}
