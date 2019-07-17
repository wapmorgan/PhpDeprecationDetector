<?php
namespace wapmorgan\PhpCodeFixer;

class ReportIssue
{
    const RESERVED_IDENTIFIER = 'identifier';
    const REMOVED_CONSTANT = 'constant';
    const DEPRECATED = 'deprecated';
    const VIOLATION = 'violation';
    const DEPRECATED_FUNCTION_USAGE = 'function_usage';
    const REMOVED_FUNCTION = 'function';
    const REMOVED_INI_SETTING = 'ini';
    const REMOVED_VARIABLE = 'variable';
    const DEPRECATED_FEATURE = 'deprecated_feature';
    const REMOVED = 'removed';
    public $version;

    public $file;
    public $line;

    public $category;
    public $type;

    public $text;
    public $replacement;

    public function __construct($version, $category, $type, $text, $file, $line)
    {
        $this->version = $version;
        $this->file = $file;
        $this->line = $line;
        $this->type = $type;
        $this->category = $category;
        $this->text = $text;
    }

    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;
        return $this;
    }
}