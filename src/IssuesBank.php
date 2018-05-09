<?php
namespace wapmorgan\PhpCodeFixer;

class IssuesBank {
    /** @var array */
    protected $issues = [];

    /**
     * Adds issue to the list
     * @param string $version PHP version
     * @param string $type Type of issues
     * @param array $issues List of issues
     */
    public function import($version, $type, $issues) {
        $this->issues[$version][$type] = $issues;
    }

    /**
     * Get all issues of specific type.
     * @param $type
     * @return array
     */
    public function getAll($type) {
        $all = array();
        foreach ($this->issues as $version => $issues) {
            if (isset($issues[$type])) {
                foreach ($issues[$type] as $issue_name => $issue_value) {
                    if (is_int($issue_name)) {
                        /**
                         * If issue does not have a key (for example, function does not have a replacement), it sets key to that function.
                         * That because scanner looks for methods/functions/variables in keys, not in values.
                         * Exception: if issue with type `function_usage` does not have a key, it just adds with numeric key.
                         */
                        if ($type !== 'functions_usage')
                            $all[$issue_value] = array($issue_value, $version);
                        else
                            $all[] = array($issue_value, $version);
                    } else
                        $all[$issue_name] = array($issue_value, $version);
                }
            }
        }
        return $all;
    }
}