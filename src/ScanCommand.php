<?php
namespace wapmorgan\PhpCodeFixer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ScanCommand extends Command
{
    protected $args;

    const STDOUT = 1;
    const JSON = 2;

    /**
     * @var PhpCodeFixer
     */
    protected $analyzer;

    /** @var string */
    protected $target;

    /** @var array */
    protected $excludeList = [];

    /** @var array */
    protected $fileExtensions = [];

    /**
     * @var string[]
     */
    protected $skippedChecks = [];

    /**
     * @var Report[]
     */
    protected $reports;

    /**
     * @var boolean
     */
    protected $hasIssue;

    /**
     * @var string
     */
    protected $jsonOutputPath;

    /**
     * @var int
     */
    protected $outputMode = self::STDOUT;

    /**
     *
     */
    protected function configure()
    {
        $this->setName('scan')
            ->setDescription('Analyzes PHP code and searches issues with deprecated functionality in newer interpreter versions.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('target', 't', InputOption::VALUE_OPTIONAL,
                        'Sets target PHP interpreter version.', end(PhpCodeFixer::$availableTargets)),
                    new InputOption('exclude', 'e', InputOption::VALUE_OPTIONAL,
                        'Sets excluded file or directory names for scanning. If need to pass few names, join it with comma.'),
                    new InputOption('max-size', 's', InputOption::VALUE_OPTIONAL,
                        'Sets max size of php file. If file is larger, it will be skipped.',
                        '1mb'),
                    new InputOption('file-extensions', null, InputOption::VALUE_OPTIONAL,
                        'Sets file extensions to be parsed.',
                        implode(', ', PhpCodeFixer::$defaultFileExtensions)),
                    new InputOption('skip-checks', null, InputOption::VALUE_OPTIONAL,
                        'Skip all checks containing any of the given values. Pass a comma-separated list for multiple values.'),
                    new InputOption('output-json', null, InputOption::VALUE_OPTIONAL,
                        'Path to store json-file with analyze results. If \'-\' passed, json will be printed on stdout.'),
                    new InputArgument('files', InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                        'Which files you want to analyze (separate multiple names with a space)?'),
                ])
            );
    }

    /**
     * Runs console application
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->jsonOutputPath = $input->getOption('output-json');
            if (!empty($this->jsonOutputPath))
                $this->outputMode = self::JSON;

            $this->analyzer = $this->configureAnalyzer(new PhpCodeFixer(), $input, $output);
            $this->analyzer->initializeIssuesBank();
            $this->scanFiles($input->getArgument('files'));
            $this->outputAnalyzeResult($output);
            if ($this->hasIssue)
                return 1;
        } catch (ConfigurationException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
            return 1;
        }

        return 0;
    }

    /**
     *
     * @param PhpCodeFixer $analyzer
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return PhpCodeFixer
     * @throws ConfigurationException
     */
    public function configureAnalyzer(PhpCodeFixer $analyzer, InputInterface $input, OutputInterface $output)
    {
        $this->setTarget($analyzer, $input->getOption('target'), $output);
        $this->setMaxSize($analyzer, $input->getOption('max-size'), $output);
        $this->setExcludeList($analyzer, $input->getOption('exclude'), $output);
        $this->setFileExtensions($analyzer, $input->getOption('file-extensions'), $output);
        $this->setSkipChecks($analyzer, $input->getOption('skip-checks'), $output);

        return $analyzer;
    }

    /**
     * Checks --target argument
     * @param PhpCodeFixer $analyzer
     * @param $value
     * @param OutputInterface $output
     * @throws ConfigurationException
     */
    public function setTarget(PhpCodeFixer $analyzer, $value, OutputInterface $output)
    {
        if (empty($value)) {
            $analyzer->setTarget($this->target = end(PhpCodeFixer::$availableTargets));
        } else {
            if (!in_array($value, PhpCodeFixer::$availableTargets, true))
                throw new ConfigurationException('Target version is not valid. Available target version: '.implode(', ', PhpCodeFixer::$availableTargets));
            $analyzer->setTarget($this->target = $value);
        }
    }

    /**
     * Checks --max-size argument
     * @param PhpCodeFixer $analyzer
     * @param $value
     * @param OutputInterface $output
     */
    public function setMaxSize(PhpCodeFixer $analyzer, $value, OutputInterface $output)
    {
        $size_units = ['b', 'kb', 'mb', 'gb'];
        if (!empty($value)) {
            foreach ($size_units as $unit) {
                if (stripos($value, $unit) > 0) {
                    $max_size_value = (int)stristr($value, $unit, true);
                    $max_size = $max_size_value * pow(1024, array_search($unit, $size_units));
                }
            }

            if (isset($max_size)) {
                if ($this->isVerbose())
                    $output->writeln('<info>Max file size set to: '.$this->formatSize('%.3F Ui', $max_size).'</info>');

                $analyzer->setFileSizeLimit($max_size);
            }
        }
    }

    /**
     * Checks --exclude argument
     */
    protected function setExcludeList(PhpCodeFixer $analyzer, $value, OutputInterface $output)
    {
        if (!empty($value)) {
            $this->excludeList = array_map('strtolower', array_map(function ($dir) { return trim($dir, '/\\ '); }, explode(',', $value)));

            if ($this->isVerbose())
                $output->writeln('<info>Excluding following files / directories: '.implode(', ', $this->excludeList).'</info>');

            $analyzer->setExcludeList($this->excludeList);
        }
    }

    /**
     * Checks --file-extensions argument
     */
    protected function setFileExtensions(PhpCodeFixer $analyzer, $value, OutputInterface $output)
    {
        if (!empty($value)) {
            $exts = array_map('strtolower', array_map('trim', explode(',', $value)));
            if ($exts !== PhpCodeFixer::$defaultFileExtensions) {
                $analyzer->setFileExtensions($exts);

                if ($this->isVerbose())
                    $output->writeln('<info>File extensions set to: '.implode(', ', $exts).'</info>');
            }
        }
    }

    /**
     * @param $skippedChecks
     * @param OutputInterface $output
     */
    public function setSkipChecks(PhpCodeFixer $analyzer, $skippedChecks, OutputInterface $output)
    {
        if (!empty($skippedChecks)) {
            $this->skippedChecks = array_map('strtolower', explode(',', $skippedChecks));

            if ($this->isVerbose())
                $output->writeln('<info>Skipping checks containing any of the following values: ' . implode(', ', $this->skippedChecks).'</info>');

            $analyzer->setExcludedChecks($this->skippedChecks);
        }
    }

    /**
     * Runs analyzer
     * @param array $files
     */
    protected function scanFiles(array $files)
    {
        $this->reports = [];
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->reports[] = $this->analyzer->checkDir(rtrim(realpath($file), DIRECTORY_SEPARATOR));
            } else if (is_file($file)) {
                $report = new Report('File '.basename($file), dirname(realpath($file)));
                $this->reports[] = $this->analyzer->checkFile(realpath($file), $report);
            }
        }
    }

    /**
     * Prints analyzer report
     * @param OutputInterface $output
     * @return int
     */
    protected function outputToStdout(OutputInterface $output)
    {
        $current_php = substr(PHP_VERSION, 0, 3);

        $this->hasIssue = false;
        $total_issues = 0;

        $output->getFormatter()->setStyle('removed_issue', new OutputFormatterStyle('red', null, [/*'bold', 'blink'*/]));
        $output->getFormatter()->setStyle('changed_issue', new OutputFormatterStyle('yellow', null, [/*'bold', 'blink'*/]));
        $output->getFormatter()->setStyle('violation_issue', new OutputFormatterStyle('red', null, ['bold', /*'blink'*/]));

        if (!empty($this->reports)) {
            $replace_suggestions = $notes = [];

            foreach ($this->reports as $report) {
                $output->writeln(null);
                $output->writeln('<fg=white>'.$report->getTitle().'</>');

                $info_messages = $report->getInfo();
                if (!empty($info_messages)) {
                    foreach ($info_messages as $message) {
                        switch ($message[0]) {
                            case Report::INFO_MESSAGE:
                                $output->writeln('<fg=yellow>'.$message[1].'</>');
                                break;
                            case Report::INFO_WARNING:
                                $output->writeln('<fg=red>'.$message[1].'</>');
                                break;
                        }
                    }
                }

                $report_issues = $report->getIssues();
                if (!empty($report)) {
					$table = new Table($output);
					$table
						->setHeaders([/*'PHP',*/ 'File:Line', 'Type', 'Issue']);
                    $versions = array_keys($report_issues);
                    sort($versions);

                    // print issues by version
                    foreach ($versions as $version) {

                        $issues = $report_issues[$version];
                        if (strcmp($current_php, $version) >= 0)
                            $output->writeln('<fg=yellow>- PHP '.$version.' ('.count($issues).') - your version is greater or equal</>');
                        else
                            $output->writeln('<fg=yellow>- PHP '.$version.' ('.count($issues).')</>');
                        $table->setRows($rows = []);

                        // iterate issues
                        foreach ($issues as $issue) {
                            $this->hasIssue = true;
                            $total_issues++;
                            switch ($issue->type) {
                                case 'function':
                                case 'function_usage':
                                    $color = 'yellow';
                                    break;

                                case 'variable':
                                    $color = 'red';
                                    break;

                                case 'ini':
                                    $color = 'green';
                                    break;

                                case 'identifier':
                                    $color = 'blue';
                                    break;

                                case 'constant':
                                    $color = 'gray';
                                    break;

                                default:
                                    $color = 'yellow';
                                    break;
                            }

                            $issue_text = sprintf('%s <%s_issue>%s</%s_issue> is %s.%s',
                                str_replace('_', ' ', ucfirst($issue->type)),
                                $issue->category,
                                $issue->text.($issue->type === ReportIssue::REMOVED_FUNCTION ? '()' : null),
                                $issue->category,
                                $issue->type === ReportIssue::RESERVED_IDENTIFIER ? 'reserved by PHP core' : $issue->category,
                                !empty($issue->replacement)
                                    ? "\n".($issue->category === ReportIssue::CHANGED
                                        ? '<comment>'.$issue->replacement.'</comment>'
                                        : 'Consider replace with <info>'.$issue->replacement
                                            .($issue->type === ReportIssue::REMOVED_FUNCTION ? '()' : null)
                                    .'</info>')
                                    : null
                            );

							$rows[] = [
								'<comment>'.$issue->file.'</comment>:'.$issue->line,
								$issue->category,
                                $issue_text,
							];
                        }

                        if (!empty($rows)) {
                        	$table->setRows($rows);
                        	$table->render();
						}
                        $output->writeln('');
                    }
                }
            }

            $output->writeln(null);

            if ($total_issues > 0)
                $output->writeln('<bg=red;fg=white>Total issues: '.$total_issues.'</>');
            else
                $output->writeln('<bg=green;fg=white>Analyzer has not detected any issues in your code.</>');
        }

        return $total_issues;
    }

    /**
     * Prints memory consumption
     */
    protected function printMemoryUsage(OutputInterface $output)
    {
        $output->writeln('<info>Peak memory usage: '.$this->formatSize('%.3F U', memory_get_peak_usage(), 'mb').'</info>');
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
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
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
     * @param $jsonFile
     * @return int
     */
    protected function outputToJson($jsonFile)
    {
        $data = [
            'info_messages' => [],
            'problems' => [],
            'replace_suggestions' => [],
            'notes' => [],
        ];

        $total_issues = 0;
        if (!empty($this->reports)) {

            foreach ($this->reports as $report) {
                $info_messages = $report->getInfo();
                if (!empty($info_messages)) {
                    foreach ($info_messages as $message) {
                        $data['info_messages'][] = [
                            'type' => $message[0] === Report::INFO_MESSAGE ? 'info' : 'warning',
                            'message' => $message[1]
                        ];
                    }
                }

                $report_issues = $report->getIssues();
                if (!empty($report)) {
                    $versions = array_keys($report_issues);
                    sort($versions);

                    // print issues by version
                    foreach ($versions as $version) {
                        // iterate issues
                        foreach ($report_issues[$version] as $issue) {
                            $this->hasIssue = true;
                            $total_issues++;

                            $data['problems'][] = [
                                'version' => $version,
                                'file' => $issue->file,
                                'path' => $report->getRemovablePath().$issue->file,
                                'line' => $issue->line,
                                'category' => $issue->category,
                                'type' => $issue->type,
                                'checker' => $issue->text,
                            ];

                            if (!empty($issue->replacement)) {
                                if ($issue->category === ReportIssue::CHANGED) {
                                    $data['notes'][] = [
                                        'type' => $issue->type,
                                        'problem' => $issue->text,
                                        'note' => $issue->replacement,
                                    ];
                                } else {
                                    $data['replace_suggestions'][] = [
                                        'type' => $issue->type,
                                        'problem' => $issue->text.($issue->type === 'function' ? '()' : null),
                                        'replacement' => $issue->replacement.($issue->type === 'function' ? '()' : null),
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        $json = json_encode(array_filter($data, function ($value) {
            return count($value) > 0;
        }), JSON_PRETTY_PRINT);

        if ($jsonFile === '-')
            fwrite(STDOUT, $json);
        else
            file_put_contents($jsonFile, $json);

        return $total_issues;
    }

    /**
     * @param OutputInterface $output
     */
    protected function outputAnalyzeResult(OutputInterface $output)
    {
        switch ($this->outputMode) {
            case self::STDOUT:
                $this->outputToStdout($output);
                $this->printMemoryUsage($output);
                break;

            case self::JSON:
                $total_issues = $this->outputToJson($this->jsonOutputPath);
                if ($this->isVerbose()) {
                    if ($total_issues > 0)
                        $output->writeln('<bg=red;fg=white>Total problems: ' . $total_issues . '</>');
                    else
                        $output->writeln('<bg=green;fg=white>Analyzer has not detected any problems in your code.</>');
                    $this->printMemoryUsage($output);
                }
                break;
        }

    }

    /**
     * Returns flag that extra information can be printed on stdout
     * @return bool
     */
    protected function isVerbose()
    {
        return $this->outputMode !== self::JSON || $this->jsonOutputPath !== '-';
    }
}