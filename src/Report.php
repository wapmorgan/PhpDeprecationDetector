<?php
namespace wapmorgan\PhpCodeFixer;

class Report {
    protected $records = array();

    public function add($version, $type, $text, $replacement, $file, $line) {
        $this->records[$version][] = array($type, $text, $replacement, $file, $line);
    }

    public function getReport() {
        return $this->records;
    }
}
