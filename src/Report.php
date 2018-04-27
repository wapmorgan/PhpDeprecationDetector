<?php
namespace wapmorgan\PhpCodeFixer;

class Report {
    /** @var array */
    protected $records = [];

    /** @var string */
    protected $title;

    /** @var string */
    protected $removablePath;

    /**
     * Report constructor.
     * @param string|null $removablePath
     */
    public function __construct($reportTitle, $removablePath = null)
    {
        $this->title = $reportTitle;
        if ($removablePath !== null)
            $this->removablePath = $removablePath;
    }

    /**
     * @param $version
     * @param $type
     * @param $text
     * @param $replacement
     * @param $file
     * @param $line
     */
    public function add($version, $type, $text, $replacement, $file, $line) {
        if ($this->removablePath !== null && strncasecmp($file, $this->removablePath, strlen($this->removablePath)) === 0)
            $file = substr($file, strlen($this->removablePath));
        $this->records[$version][] = [$type, $text, $replacement, $file, $line];
    }

    /**
     * @return array
     */
    public function getIssues() {
        return $this->records;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
