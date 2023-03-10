<?php
namespace wapmorgan\PhpCodeFixer;

class ReportIssue
{
    // categories
    const CHANGED = 'changed';
    const VIOLATION = 'violation';
    const REMOVED = 'removed';

    // types
    const RESERVED_IDENTIFIER = 'identifier';
    const DEPRECATED_FEATURE = 'deprecated_feature';
    const DEPRECATED_FUNCTION_USAGE = 'function_usage';
    const REMOVED_CONSTANT = 'constant';
    const REMOVED_FUNCTION = 'function';
    const REMOVED_INI_SETTING = 'ini';
    const REMOVED_VARIABLE = 'variable';
    public $version;

    public $path;
    public $file;
    public $line;
    public $column;

    public $category;
    public $type;

    public $text;
    public $replacement;

    public function __construct($version, $category, $type, $text, $path, $file, $line, $column)
    {
        $this->version = $version;
        $this->path = $path;
        $this->file = $file;
        $this->line = $line;
        $this->column = $column;
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
