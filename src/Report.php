<?php
namespace wapmorgan\PhpCodeFixer;

class Report {
    /** @var array */
    protected $records = [];

    /** @var string */
    protected $title;

    /** @var string */
    protected $removablePath;

    const INFO_MESSAGE = 1;
    const INFO_WARNING = 2;

    /**
     * @var array[] Info messages
     */
    protected $info;

    /**
     * Report constructor.
     * @param string $reportTitle Title of report
     * @param string|null $removablePath If present, this part of paths will be removed from added issues
     */
    public function __construct($reportTitle, $removablePath = null)
    {
        $this->title = $reportTitle;
        if ($removablePath !== null)
            $this->removablePath = $removablePath;
    }

    /**
     * Adds issue to the report
     * @param string $version PHP version
     * @param string $type Issue type
     * @param string $text Issue description
     * @param string|null $replacement Possible replacement
     * @param string $file File in which issue present
     * @param integer $line Line of file
     * @return void
     */
    public function addProblem($version, $type, $text, $replacement, $file, $line) {
        if ($this->removablePath !== null && strncasecmp($file, $this->removablePath, strlen($this->removablePath)) === 0)
            $file = substr($file, strlen($this->removablePath));
        $this->records[$version][] = [$type, $text, $replacement, $file, $line];
    }

    /**
     * @param $category
     * @param $message
     */
    public function addInfo($category, $message)
    {
        $this->info[] = [$category, $message];
    }

    /**
     * @return array[]
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Returns list of all issues
     * @return array
     */
    public function getIssues() {
        return $this->records;
    }

    /**
     * Returns title of report
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getRemovablePath()
    {
        return $this->removablePath;
    }
}
