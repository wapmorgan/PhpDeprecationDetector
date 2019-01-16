<?php
namespace wapmorgan\PhpCodeFixer;

use Exception;

class Application
{
    protected $args;

    /** @var Report[] */
    protected $reports;

    /** @var boolean */
    protected $hasIssue;

    /**
     * @var PhpCodeFixer
     */
    protected $analyzer;

    /**
     * Application constructor.
     * @param $args
     */
    public function __construct($args)
    {
        $this->args = $args;
        $this->analyzer = new PhpCodeFixer();
    }

    /**
     * Runs console application
     */
    public function run()
    {
        try {
            $this->setTarget();
            $this->setMaxSize();
            $this->setExcludeList();
            $this->setSkipChecks();
            $this->setFileExtensions();

            $this->scanFiles();
            $this->printReport();
            $this->printMemoryUsage();
            if ($this->hasIssue)
                exit(1);
        } catch (Exception $e) {
            $this->exitWithError($e->getMessage(), 128);
        }
    }

    /**
     * Checks --target argument
     * @throws Exception
     */
    public function setTarget()
    {
        if (empty($this->args['--target'])) {
            $this->analyzer->setTargetPhpVersion(PhpCodeFixer::getLatestSupportedTargetVersion());
        } else {
            $this->analyzer->setTargetPhpVersion($this->args['--target']);
        }
    }

    /**
     * Checks --max-size argument
     */
    public function setMaxSize()
    {
        static $size_units = ['kb', 'mb', 'gb'];
        if (!empty($this->args['--max-size'])) {
            foreach ($size_units as $unit) {
                if (stripos($this->args['--max-size'], $unit) > 0) {
                    $max_size_value = (int)stristr($this->args['--max-size'], $unit, true);
                    $max_size = $max_size_value * pow(1024, array_search($unit, $size_units) + 1);
                    break;
                }
            }
            if (!isset($max_size)) {
                if ((int)$this->args['--max-size'] > 0)
                    $max_size = (int)$this->args['--max-size'];
            }

            if (isset($max_size)) {
                $this->analyzer->setFileSizeLimit($max_size);
                $this->echoInfoLine('Max file size set to: ' . $this->formatSize('%.3F Ui', $max_size));
            }
        }
    }

    /**
     * Checks --exclude argument
     */
    protected function setExcludeList()
    {
        if (!empty($this->args['--exclude'])) {
            $this->analyzer->setExcludedFilesList($excluded = array_map(
                function ($dir) { return trim(strtolower($dir), '/\\ '); },
                explode(',', $this->args['--exclude'])));
            $this->echoInfoLine('Excluding the following files / directories: '.implode(', ', $excluded));
        }
    }

    /**
     * Checks --exclude- argument
     */
    protected function setSkipChecks()
    {
        if (!empty($this->args['--skip-checks'])) {
            $this->analyzer->setSkippedChecks($skipped_checks = array_map('strtolower', explode(',', $this->args['--skip-checks'])));
            $this->echoInfoLine('Skipping checks containing any of the following values: '.implode(', ', $skipped_checks));
        }
    }

    /**
     * Checks --file-extensions argument
     */
    protected function setFileExtensions()
    {
        if (!empty($this->args['--file-extensions'])) {
            $exts = array_map('strtolower', array_map('trim', explode(',', $this->args['--file-extensions'])));
            if (!empty($exts)) {
                $this->analyzer->setFileExtensions($exts);
                $this->echoInfoLine('File extensions set to: '.implode(', ', $exts));
            }
        }
    }

    /**
     * Runs analyzer
     */
    protected function scanFiles()
    {
        $this->analyzer->initialize();
        $this->reports = [];
        foreach ($this->args['FILES'] as $file) {
            if (is_dir($file)) {
                TerminalInfo::echoWithColor('Scanning '.$file.' ...'.PHP_EOL, TerminalInfo::GRAY_TEXT);
                $this->reports[] = $this->analyzer->checkDir(rtrim(realpath($file), DIRECTORY_SEPARATOR));
            } else if (is_file($file)) {
                $report = new Report('File '.basename($file), dirname(realpath($file)));
                $report = $this->analyzer->checkFile(realpath($file), $report);
                if($report instanceof Report) {
                    $this->reports[] = $report;
                }
            }
        }
    }

    /**
     * Prints analyzer report
     */
    protected function printReport()
    {
        // adjustable output width
        if (TerminalInfo::isInteractive()) {
            $width = TerminalInfo::getWidth();
        } else {
            $width = 80;
        }

        $current_php = substr(PHP_VERSION, 0, 3);

        $variable_length = max(30, floor(($width - 31) * 0.4));
        $this->hasIssue = false;

        if (!empty($this->reports)) {
            $total_issues = 0;
            $replace_suggestions = $notes = [];

            foreach ($this->reports as $report) {
                echo PHP_EOL;
                TerminalInfo::echoWithColor($report->getTitle().PHP_EOL, TerminalInfo::WHITE_TEXT);

                $report = $report->getIssues();
                if (!empty($report)) {
                    echo sprintf(' %3s | %-' . $variable_length . 's | %16s | %s', 'PHP', 'File:Line', 'Type', 'Issue') . PHP_EOL;
                    $versions = array_keys($report);
                    sort($versions);

                    // print issues by version
                    foreach ($versions as $version) {
                        $issues = $report[$version];

                        // iterate issues
                        foreach ($issues as $issue) {
                            $this->hasIssue = true;
                            $total_issues++;
                            switch ($issue[0]) {
                                case 'function':
                                case 'function_usage':
                                    $color = TerminalInfo::YELLOW_TEXT;
                                    break;

                                case 'variable':
                                    $color = TerminalInfo::RED_TEXT;
                                    break;

                                case 'ini':
                                    $color = TerminalInfo::GREEN_TEXT;
                                    break;

                                case 'identifier':
                                    $color = TerminalInfo::BLUE_TEXT;
                                    break;

                                case 'constant':
                                    $color = TerminalInfo::GRAY_TEXT;
                                    break;

                                default:
                                    $color = TerminalInfo::YELLOW_TEXT;
                                    break;
                            }

                            $line_length = strlen($issue[4]);

                            echo sprintf(' %3s | %-' . ($variable_length + (TerminalInfo::isColorsCapable() ? 22 : 0)) . 's | %-16s | %s',
                                    strcmp($current_php, $version) >= 0 ? TerminalInfo::colorize($version, TerminalInfo::RED_BACKGROUND) : $version,
                                    TerminalInfo::colorize($this->normalizeAndTruncatePath($issue[3], $variable_length - $line_length - 1), TerminalInfo::WHITE_TEXT)
                                        .':'.TerminalInfo::colorize($issue[4], TerminalInfo::GRAY_TEXT),
                                    $issue[0],
                                    str_replace('_', ' ', ucfirst($issue[0])) . ' ' . TerminalInfo::colorize($issue[1].($issue[0] == 'function' ? '()' : null), $color)
                                    . ' is '
                                    . ($issue[0] == 'identifier' ? 'reserved by PHP core' : 'deprecated') . '. ') . PHP_EOL;

                            if (!empty($issue[2])) {
                                if ($issue[0] === 'function_usage') {
                                    $notes[$issue[0]][$issue[1]] = $issue[2];
                                }
                                else
                                    $replace_suggestions[$issue[0]][$issue[1]] = $issue[2];
                            }
                        }
                    }
                }
            }

            echo PHP_EOL;
            if ($total_issues > 0)
                TerminalInfo::echoWithColor(TerminalInfo::colorize('Total problems: '.$total_issues, TerminalInfo::RED_BACKGROUND).PHP_EOL, TerminalInfo::WHITE_TEXT);
            else
                TerminalInfo::echoWithColor(TerminalInfo::colorize('Analyzer has not detected any problems in your code.', TerminalInfo::GREEN_BACKGROUND).PHP_EOL, TerminalInfo::WHITE_TEXT);

            if (!empty($replace_suggestions)) {
                echo PHP_EOL;
                TerminalInfo::echoWithColor('Replace Suggestions:'.PHP_EOL, TerminalInfo::WHITE_TEXT);
                $i = 1;
                foreach ($replace_suggestions as $type => $suggestion) {
                    foreach ($suggestion as $issue => $replacement) {
                        echo ($i++).'. Don\'t use '.$type.' '
                            .TerminalInfo::colorize($issue.($type === 'function' ? '()' : null), TerminalInfo::RED_UNDERLINED_TEXT)
                            .' => Consider replace to '.TerminalInfo::colorize($replacement.($type === 'function' ? '()' : null), TerminalInfo::GREEN_TEXT).'.'.PHP_EOL;
                    }
                }
            }

            if (!empty($notes)) {
                echo PHP_EOL;
                TerminalInfo::echoWithColor('Notes:'.PHP_EOL, TerminalInfo::WHITE_TEXT);
                $i = 1;
                foreach ($notes as $type => $note) {
                    foreach ($note as $issue => $issue_note) {
                        echo ($i++).'. Usage '.TerminalInfo::colorize($issue, TerminalInfo::RED_UNDERLINED_TEXT)
                            .': '.TerminalInfo::colorize($issue_note, TerminalInfo::WHITE_TEXT).PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * Prints memory consumption
     */
    protected function printMemoryUsage()
    {
        $this->echoInfoLine('Peak memory usage: '.$this->formatSize('%.3F U', memory_get_peak_usage(), 'mb'));
    }

    /**
     * Simplifies path to fit in specific width
     * @param string $path
     * @param integer $maxLength
     * @return string
     */
    public function normalizeAndTruncatePath($path, $maxLength) {
        $truncated = 1;
        $path_parts = explode('/', str_replace('\\', '/', $path));
        $total_parts = count($path_parts);

        while (strlen($path) > $maxLength) {
            if (($truncated + 1) === $total_parts) break;
            $part_to_modify = $total_parts - 1 - $truncated;
            $chars_to_truncate = min(strlen($path_parts[$part_to_modify]) - 1, strlen($path) - $maxLength);
            if ((strlen($path) - $maxLength + 2) < strlen($path_parts[$part_to_modify]))
                $chars_to_truncate += 2;

            $path_parts[$part_to_modify] = substr($path_parts[$part_to_modify], 0, -$chars_to_truncate).'..';
            $path = implode('/', $path_parts);
            $truncated++;
        }

        return $path;
    }

    /**
     * @param string $format Sets format for size.
     * Format should containt string parsable by sprintf() function and contain one %F macro that will be replaced by size. Another macro is U/u. It will be replaced with used unit. U for uppercase, u - lowercase. If 'i' is present at the end of format string, size multiplier will be set to 1024 (and units be KiB, MiB and so on), otherwise multiplier is set to 1000.
     * @example "%.0F Ui" 617 KiB
     * @example "%.3F Ui" 617.070 KiB
     * @example "%10.3F Ui"     616.85 KiB
     * @example "%.3F U" 632.096 KB
     *
     * @param integer $bytes Size in bytes
     * @param string $unit Sets default unit. Can have these values: B, KB, MG, GB, TB, PB, EB, ZB and YB
     * @return string
     */
    public function formatSize($format, $bytes, $unit = null) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $bytes = max($bytes, 0);
        $unit = strtoupper($unit);

        if (substr($format, -1) === 'i') {
            $multiplier = 1024;
            $format = substr($format, 0, -1);
        }
        else
            $multiplier = 1000;

        if ($unit === null || !in_array($unit, $units)) {
            $pow = floor(($bytes ? log($bytes) : 0) / log($multiplier));
            $pow = min($pow, count($units) - 1);

            $bytes /= pow($multiplier, $pow);
            $unit = $units[$pow];
        } else {
            $pow = array_search($unit, $units);
            $bytes /= pow($multiplier, $pow);
        }

        if ($multiplier == 1024)
            $unit = (strlen($unit) == 2) ? substr($unit, 0, 1).'iB' : $unit;
        if (strpos($format, 'u') !== false)
            $format = str_replace('u', strtolower($unit), $format);
        else
            $format = str_replace('U', $unit, $format);

        return sprintf($format, $bytes);
    }

    /**
     * Shows error and terminates script
     * @param string $message
     * @param int $code
     */
    public function exitWithError($message, $code = 128)
    {
        fwrite(STDERR, TerminalInfo::colorize($message, TerminalInfo::RED_BACKGROUND).PHP_EOL);
        exit($code);
    }

    /**
     * Prints information message
     * @param string $message
     */
    public function echoInfoLine($message)
    {
        TerminalInfo::echoWithColor($message.PHP_EOL, TerminalInfo::GRAY_TEXT);
    }
}