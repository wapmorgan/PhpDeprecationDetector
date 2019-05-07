<?php
namespace wapmorgan\PhpCodeFixer;

class IssuesBank {
    /** @var array<string $version, array<string $type, array $issues>> */
    protected $issues = [];

    /**
     * Adds issue to the list
     * @param string $version PHP version
     * @param string $type Type of issues
     * @param array $issues List of issues
     * @param array $checksFilters
     */
    public function import($version, $type, $issues, array $checksFilters = []) {
        // filter by value
        $issues = self::filterCheckers($issues, $checksFilters);
        $filtered_keys = self::filterCheckers(array_keys($issues), $checksFilters);

        $this->issues[$version][$type] = array_intersect_key($issues, array_flip($filtered_keys));
    }

    /**
     * @param array $checkers
     * @param array $disallowedChecks
     * @return array
     */
    protected static function filterCheckers($checkers, array $disallowedChecks)
    {
        return array_filter($checkers, function ($value) use ($disallowedChecks) {
            foreach ($disallowedChecks as $disallowedCheck) {
                if (stripos($value, $disallowedCheck) !== false) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Get all issues of specific type.
     * @param $type
     * @return array
     */
    public function getAll($type) {
        $all = [];
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
                            $all[$issue_value] = [$issue_value, $version];
                        else
                            $all[] = [$issue_value, $version];
                    } else
                        $all[$issue_name] = [$issue_value, $version];
                }
            }
        }
        return $all;
    }
}