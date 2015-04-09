<?php
namespace wapmorgan\PhpCodeFixer;

class IssuesBank {
    protected $issues = array();

    public function import($version, $type, $issues) {
        $this->issues[$version][$type] = $issues;
    }

    public function getAll($type) {
        $all = array();
        foreach ($this->issues as $version => $issues) {
            if (isset($issues[$type])) {
                foreach ($issues[$type] as $issue_name => $issue_value) {
                    if (is_int($issue_name))
                        $all[$issue_value] = array($issue_value, $version);
                    else
                        $all[$issue_name] = array($issue_value, $version);
                }
            }
        }
        return $all;
    }
}