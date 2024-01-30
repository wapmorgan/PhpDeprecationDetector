# PhpDeprecationDetector

PhpDeprecationDetector - analyzer of PHP code to search usages of deprecated functionality in newer interpreter versions - deprecations detector.

[![Latest Stable Version](https://poser.pugx.org/wapmorgan/php-deprecation-detector/v/stable)](https://packagist.org/packages/wapmorgan/php-deprecation-detector)
[![Total Downloads](https://poser.pugx.org/wapmorgan/php-deprecation-detector/downloads)](https://packagist.org/packages/wapmorgan/php-deprecation-detector)
[![License](https://poser.pugx.org/wapmorgan/php-deprecation-detector/license)](https://packagist.org/packages/wapmorgan/php-deprecation-detector)

PhpDeprecationDetector detects:
- Usage of deprecated **functions, variables, constants and ini-directives**.
- Usage of deprecated **functions functionality**.
- Usage of **forbidden names or tricks** (e.g. reserved identifiers in newer versions).

It literally helps you find code that can fail after migration to newer PHP version.

1. [Installation](#installation)
2. [Usage](#usage)
  - [Console scanner](#console-scanner)
  - [Json report format](#json-report-format)

# Installation

## Phar file

1. Just download a phar from [releases page](https://github.com/wapmorgan/PhpDeprecationDetector/releases) and make executable
  ```sh
  chmod +x phpdd-x.x.x.phar
  ```

2. a. **Local installation**: use it from current folder:
    ```php
    ./phpdd-x.x.x.phar -h
    ```

   b. **Global installation**: move it in to one of folders listed in your `$PATH` and run from any folder:
    ```sh
    sudo mv phpdd-x.x.x.phar /usr/local/bin/phpdd
    phpdd -h
    ```

## Composer
Another way to install _phpdd_ is via composer.

1. Install composer:
  ```sh
  curl -sS https://getcomposer.org/installer | php
  ```

2. Install phpdd in global composer dir:
  ```sh
  ./composer.phar global require wapmorgan/php-deprecation-detector dev-master
  ```

3. Run from any folder:
  ```sh
  phpdd -h
  ```

# Usage
## Console scanner
To scan your files or folder launch `phpdd` and pass file or directory names.

```
Description:
  Analyzes PHP code and searches issues with deprecated functionality in newer interpreter versions.

Usage:
  scan [options] [--] <files>...

Arguments:
  files                                    Which files you want to analyze (separate multiple names with a space)?

Options:
  -t, --target[=TARGET]                    Sets target PHP interpreter version. [default: "8.0"]
  -a, --after[=AFTER]                      Sets initial PHP interpreter version for checks. [default: "5.3"]
  -e, --exclude[=EXCLUDE]                  Sets excluded file or directory names for scanning. If need to pass few names, join it with comma.
  -s, --max-size[=MAX-SIZE]                Sets max size of php file. If file is larger, it will be skipped. [default: "1mb"]
      --file-extensions[=FILE-EXTENSIONS]  Sets file extensions to be parsed. [default: "php, php5, phtml"]
      --skip-checks[=SKIP-CHECKS]          Skip all checks containing any of the given values. Pass a comma-separated list for multiple values.
      --output[=OUTPUT]                    The output type required. Options: stdout, json, junit. Defaults to stdout.
      --output-file[=OUTPUT-FILE]          File path to store results where output is not stdout.
  -h, --help                               Display help for the given command. When no command is given display help for the scan command
  -q, --quiet                              Do not output any message
  -V, --version                            Display this application version
      --ansi                               Force ANSI output
      --no-ansi                            Disable ANSI output
  -n, --no-interaction                     Do not ask any interactive question
  -v|vv|vvv, --verbose                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

- By providing additional option `--target` you can specify version of PHP to perform less checks. Available target versions: 5.3, 5.4, 5.5, 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0. A larger version includes rules for checking from all previous.
- By providing `--exclude` option you can exclude specific folders or files from analyze. For example, `--exclude vendor` will prevent checking third-party libraries.
- By providing `--skip-checks` option you can exclude specific checks from analyze.
- If your files has unusual extension, you can specify all exts by `--file-extensions` option. By default, it uses `php`, `phtml` and `php5`.
- If you need to generate machine-readable analyze result, use the `--output-file` option to specify path to store the output file as specified on `--output` (json or junit).

### Example of usage
```
> ./bin/phpdd tests/
Max file size set to: 1.000 MiB
Folder /media/wapmorgan/Локальный диск/Документы/PhpDeprecationDetector/tests
- PHP 5.3 (3) - your version is greater or equal
+------------+---------+---------------------------------------------------------------------+
| File:Line  | Type    | Issue                                                               |
+------------+---------+---------------------------------------------------------------------+
| /5.3.php:2 | removed | Function dl() is removed.                                           |
| /5.3.php:3 | removed | Ini define_syslog_variables is removed.                             |
| /5.3.php:5 | changed | Function usage piet() (@call_with_passing_by_reference) is changed. |
|            |         | Call with passing by reference is deprecated. Problem is "&$hoho"   |
+------------+---------+---------------------------------------------------------------------+

- PHP 5.4 (2) - your version is greater or equal
+------------+---------+-----------------------------------------------+
| File:Line  | Type    | Issue                                         |
+------------+---------+-----------------------------------------------+
| /5.4.php:2 | removed | Function mcrypt_generic_end() is removed.     |
|            |         | Consider replace with mcrypt_generic_deinit() |
| /5.4.php:3 | removed | Function magic_quotes_runtime() is removed.   |
+------------+---------+-----------------------------------------------+
...
...
...
```

## Json report format
Also, you can store analyze result in json format for automatic check. Pass `--output-file=FILENAME` to write result to **FILENAME** file or do not set to output to *stdout*.

## Junit report format
Also, you can store analyze result in junit format for automatic check. Pass `--output-file=FILENAME` to write result to **FILENAME** file or do not set to output to *stdout*.

**Format of json** - dictionary with items:
- InfoMessage[] **info_messages** - list of information messages about analyzing process.
- Issue[] **problems** - list of issues found in your code.
- ReplaceSuggestion[] **replace_suggestions** - list of replacement suggestions based on your code.
- Note[] **notes** - list of notes about new functions behaviour.

Items description:
- **InfoMessage** structure:
  - string **type** - message type - any of (info | warning)
  - string **message** - message text
- **Issue** structure:
  - string **version** - interpreter version which has current issue (*like 7.2*)
  - string **file** - relative path to file in which issue found (*like src/ProblemClass.php*)
  - string **path** - absolute path to file in which issue found (*like /var/www/html/project/src/ProblemClass.php*)
  - int **line** - line in file in which issue found
  - int **column** - column in line in which issue found
  - string **category** - issue category - any of (changed | removed | violation)
  - string **type** - concrete issue type (*like "constant" or "identifier"*)
  - string **checker** - concrete issue object which may cause problem (*like `magic_quotes_runtime` or `preg_replace() (@preg_replace_e_modifier)`*)
- **ReplaceSuggestion** structure:
  - string **type** - replacement object type (*like variable or ini*)
  - string **problem** - replacement object (*like mcrypt_generic_end() or each()*)
  - string **replacement** - suggestion to replace with (*like mcrypt_generic_deinit() or foreach()*)
- **Note** structure:
  - string **type** - type of note (*like function_usage or deprecated_feature*)
  - string **problem** - note object (*like `preg_replace() (@preg_replace_e_modifier)` or `parse_str() (@parse_str_without_argument)`*)
  - string **note** - note text (*like `Usage of "e" modifier in preg_replace is deprecated: "asdasdsd~ie"` or `Call to parse_str() without second argument is deprecated`*)

# Build

```shell
docker run --rm --interactive --tty --volume $PWD:/app composer:2.2.4 sh
# and inside a container:
docker-php-ext-install bcmath
composer require macfja/phar-builder
echo phar.readonly=0 >> /usr/local/etc/php/php-cli.ini
composer run-script build
```
