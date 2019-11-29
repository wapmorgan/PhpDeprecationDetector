# PhpCodeFixer

PhpCodeFixer - analyzer of PHP code to search issues with deprecated functionality in newer interpreter versions..

[![Latest Stable Version](https://poser.pugx.org/wapmorgan/php-code-fixer/v/stable)](https://packagist.org/packages/wapmorgan/php-code-fixer)
[![Total Downloads](https://poser.pugx.org/wapmorgan/php-code-fixer/downloads)](https://packagist.org/packages/wapmorgan/php-code-fixer)
[![License](https://poser.pugx.org/wapmorgan/php-code-fixer/license)](https://packagist.org/packages/wapmorgan/php-code-fixer)

PhpCodeFixer finds:
- Usage of removed objects: functions, variables, constants and ini-directives.
- Usage of deprecated functions functionality.
- Usage of forbidden names or tricks (e.g. reserved identifiers in newer versions).

It literally helps you find code that can fail after migration to newer PHP version.

1. [Installation](#installation)
2. [Usage](#usage)
  - [Console scanner](#console-scanner)
  - [Json report format](#json-report-format)

# Installation

## Phar file

1. Just download a phar from [releases page](https://github.com/wapmorgan/PhpCodeFixer/releases) and make executable
  ```sh
  chmod +x phpcf-x.x.x.phar
  ```

2. a. **Local installation**: use it from current folder:
    ```php
    ./phpcf-x.x.x.phar -h
    ```

   b. **Global installation**: move it in to one of folders listed in your `$PATH` and run from any folder:
    ```sh
    sudo mv phpcf-x.x.x.phar /usr/local/bin/phpcf
    phpcf -h
    ```

## Composer
Another way to install _phpcf_ is via composer.

1. Install composer:
  ```sh
  curl -sS https://getcomposer.org/installer | php
  ```

2. Install phpcf in global composer dir:
  ```sh
  ./composer.phar global require wapmorgan/php-code-fixer dev-master
  ```

3. Run from any folder:
  ```sh
  phpcf -h
  ```

# Usage
## Console scanner
To scan your files or folder launch `phpcf` and pass file or directory names.

```
Usage:
    phpcf [-t|--target [TARGET]] [-e|--exclude [EXCLUDE]] [-s|--max-size [MAX-SIZE]] [--file-extensions [FILE-EXTENSIONS]] [--skip-checks [SKIP-CHECKS]] [--output-json [OUTPUT-JSON]] [--] <files> (<files>)...

Arguments:
    files                                    Which files you want to analyze (separate multiple names with a space)?

Options:
    -t, --target[=TARGET]                    Sets target PHP interpreter version. [default: "7.4"]
    -e, --exclude[=EXCLUDE]                  Sets excluded file or directory names for scanning. If need to pass few names, join it with comma.
    -s, --max-size[=MAX-SIZE]                Sets max size of php file. If file is larger, it will be skipped. [default: "1mb"]
        --file-extensions[=FILE-EXTENSIONS]  Sets file extensions to be parsed. [default: "php, php5, phtml"]
        --skip-checks[=SKIP-CHECKS]          Skip all checks containing any of the given values. Pass a comma-separated list for multiple values.
        --output-json[=OUTPUT-JSON]          Path to store json-file with analyze results.  If '-' passed, json will be printed on stdout.
```

- By providing additional option `--target` you can specify version of PHP to perform less checks. Available target versions: 5.3, 5.4, 5.5, 5.6, 7.0, 7.1, 7.2, 7.3, 7.4. A larger version includes rules for checking from all previous.
- By providing `--exclude` option you can exclude specific folders or files from analyze. For example, `--exclude vendor` will prevent checking third-party libraries.
- By providing `--skip-checks` option you can exclude specific checks from analyze.
- If your files has unusual extension, you can specify all exts by `--file-extensions` option. By default, it uses `php`, `phtml` and `php5`.
- If you need to generate machine-readable analyze result, use `--output-json` option to specify path to store json or `--output-json=-` to print json to stdout.

### Example of usage
```
> ./bin/phpcf tests/
Max file size set to: 1.000 MiB
Folder /media/wapmorgan/Локальный диск/Документы/PhpCodeFixer/tests
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
Also, you can store analyze result in json format for automatic check. Pass `--output-json=FILENAME` to write result to **FILENAME** file or `--output-json=-` to output to *stdout*.

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
