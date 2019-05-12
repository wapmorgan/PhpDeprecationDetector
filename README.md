# PhpCodeFixer

PhpCodeFixer - a console scanner that checks compatibility of your code with new interpreter versions.

[![Latest Stable Version](https://poser.pugx.org/wapmorgan/php-code-fixer/v/stable)](https://packagist.org/packages/wapmorgan/php-code-fixer)
[![Total Downloads](https://poser.pugx.org/wapmorgan/php-code-fixer/downloads)](https://packagist.org/packages/wapmorgan/php-code-fixer)
[![License](https://poser.pugx.org/wapmorgan/php-code-fixer/license)](https://packagist.org/packages/wapmorgan/php-code-fixer)

PhpCodeFixer finds:
- Usage of deprecated functionality (functions / variables / ini-directives / constants).
- Usage of functions with changed behavior.
- Usage of reserved identifiers in newer versions.

It literally helps you fix code that can fail after migration to newer PHP version.

1. [Usage](#usage)
2. [Example](#example-of-usage)
3. [Installation](#installation)

# Usage
To scan your files or folder launch `phpcf` and pass file or directory names.

```
Usage:
    phpcf [-t|--target [TARGET]] [-e|--exclude [EXCLUDE]] [-s|--max-size [MAX-SIZE]] [--file-extensions [FILE-EXTENSIONS]] [--skip-checks [SKIP-CHECKS]] [--output-json [OUTPUT-JSON]] [--] <files> (<files>)...

Arguments:
    files                                    Which files you want to analyze (separate multiple names with a space)?
  
Options:
    -t, --target[=TARGET]                    Sets target PHP interpreter version. [default: "7.3"]
    -e, --exclude[=EXCLUDE]                  Sets excluded file or directory names for scanning. If need to pass few names, join it with comma.
    -s, --max-size[=MAX-SIZE]                Sets max size of php file. If file is larger, it will be skipped. [default: "1mb"]
        --file-extensions[=FILE-EXTENSIONS]  Sets file extensions to be parsed. [default: "php, php5, phtml"]
        --skip-checks[=SKIP-CHECKS]          Skip all checks containing any of the given values. Pass a comma-separated list for multiple values.
        --output-json[=OUTPUT-JSON]          Path to store json-file with analyze results.  If '-' passed, json will be printed on stdout.
```

- By providing additional option `--target` you can specify version of PHP to perform less checks. Available target versions: 5.3, 5.4, 5.5, 5.6, 7.0, 7.1, 7.2, 7.3. A larger version includes rules for checking from all previous.
- By providing `--exclude` option you can exclude specific folders or files from analyze. For example, `--exclude vendor` will prevent checking third-party libraries.
- By providing `--skip-checks` option you can exclude specific checks from analyze.
- If your files has unusual extension, you can specify all exts by `--file-extensions` option. By default, it uses `php`, `phtml` and `php5`.
- If you need to generate machine-readable analyze result, use `--output-json` option to specify path to store json or `--output-json=-` to print json to stdout.

# Example of usage
```
> bin/phpcf tests
Max file size set to: 1.000 MiB
Folder /media/wapmorgan/HDD/Документы/PhpCodeFixer/tests
- PHP 5.3 (3) - your version is greater or equal
+------------+----------------+--------------------------------------------------------------------------+
| File:Line  | Type           | Issue                                                                    |
+------------+----------------+--------------------------------------------------------------------------+
| /5.3.php:2 | function       | Function "dl()" is deprecated.                                           |
| /5.3.php:3 | ini            | Ini "define_syslog_variables" is deprecated.                             |
| /5.3.php:5 | function_usage | Function usage "piet() (@call_with_passing_by_reference)" is deprecated. |
+------------+----------------+--------------------------------------------------------------------------+

- PHP 5.4 (2) - your version is greater or equal
+------------+----------+--------------------------------------------------+
| File:Line  | Type     | Issue                                            |
+------------+----------+--------------------------------------------------+
| /5.4.php:2 | function | Function "mcrypt_generic_end()" is deprecated.   |
| /5.4.php:3 | function | Function "magic_quotes_runtime()" is deprecated. |
+------------+----------+--------------------------------------------------+

- PHP 5.5 (1) - your version is greater or equal
+------------+----------------+---------------------------------------------------------------------------+
| File:Line  | Type           | Issue                                                                     |
+------------+----------------+---------------------------------------------------------------------------+
| /5.5.php:2 | function_usage | Function usage "preg_replace() (@preg_replace_e_modifier)" is deprecated. |
+------------+----------------+---------------------------------------------------------------------------+

- PHP 5.6 (2) - your version is greater or equal
+------------+----------+-----------------------------------------------+
| File:Line  | Type     | Issue                                         |
+------------+----------+-----------------------------------------------+
| /5.6.php:6 | ini      | Ini "mbstring.http_output" is deprecated.     |
| /5.6.php:3 | variable | Variable "$HTTP_RAW_POST_DATA" is deprecated. |
+------------+----------+-----------------------------------------------+

- PHP 7.0 (7) - your version is greater or equal
+-------------+----------------+------------------------------------------------------------------------------+
| File:Line   | Type           | Issue                                                                        |
+-------------+----------------+------------------------------------------------------------------------------+
| /7.0.php:8  | function       | Function "mssql_connect()" is deprecated.                                    |
| /7.0.php:12 | ini            | Ini "always_populate_raw_post_data" is deprecated.                           |
| /7.0.php:14 | function_usage | Function usage "password_hash() (@password_hash_salt_option)" is deprecated. |
| /7.0.php:16 | identifier     | Identifier "float" is reserved by PHP core.                                  |
| /7.0.php:17 | identifier     | Identifier "float" is reserved by PHP core.                                  |
| /7.0.php:22 | identifier     | Identifier "Int" is reserved by PHP core.                                    |
| /7.0.php:3  | method_name    | Method name "test:test (@php4_constructors)" is deprecated.                  |
+-------------+----------------+------------------------------------------------------------------------------+

- PHP 7.1 (4) - your version is greater or equal
+------------+----------------+---------------------------------------------------------------------------------+
| File:Line  | Type           | Issue                                                                           |
+------------+----------------+---------------------------------------------------------------------------------+
| /7.1.php:2 | function       | Function "mcrypt_decrypt()" is deprecated.                                      |
| /7.1.php:4 | ini            | Ini "session.hash_function" is deprecated.                                      |
| /7.1.php:7 | function_usage | Function usage "mb_ereg_replace() (@mb_ereg_replace_e_modifier)" is deprecated. |
| /7.1.php:9 | identifier     | Identifier "iterable" is reserved by PHP core.                                  |
+------------+----------------+---------------------------------------------------------------------------------+

- PHP 7.2 (7) - your version is greater or equal
+-------------+----------------+---------------------------------------------------------------------------+
| File:Line   | Type           | Issue                                                                     |
+-------------+----------------+---------------------------------------------------------------------------+
| /7.2.php:2  | function       | Function "create_function()" is deprecated.                               |
| /7.2.php:7  | function       | Function "read_exif_data()" is deprecated.                                |
| /7.2.php:14 | function       | Function "each()" is deprecated.                                          |
| /7.2.php:9  | constant       | Constant "INTL_IDNA_VARIANT_2003" is deprecated.                          |
| /7.2.php:3  | ini            | Ini "mbstring.func_overload" is deprecated.                               |
| /7.2.php:5  | function_usage | Function usage "assert() (@assert_on_string)" is deprecated.              |
| /7.2.php:12 | function_usage | Function usage "parse_str() (@parse_str_without_argument)" is deprecated. |
+-------------+----------------+---------------------------------------------------------------------------+

- PHP 7.3 (2) - your version is greater or equal
+------------+----------------+---------------------------------------------------------------------+
| File:Line  | Type           | Issue                                                               |
+------------+----------------+---------------------------------------------------------------------+
| /7.3.php:3 | constant       | Constant "FILTER_FLAG_SCHEME_REQUIRED" is deprecated.               |
| /7.3.php:2 | function_usage | Function usage "define() (@define_case_insensitive)" is deprecated. |
+------------+----------------+---------------------------------------------------------------------+

Total problems: 28

Replace Suggestions:
1. Don't use function mcrypt_generic_end() => Consider replace to mcrypt_generic_deinit().
2. Don't use function read_exif_data() => Consider replace to exif_read_data().
3. Don't use function each() => Consider replace to foreach().
4. Don't use ini mbstring.http_output => Consider replace to default_charset.
5. Don't use variable $HTTP_RAW_POST_DATA => Consider replace to php://input.
6. Don't use constant INTL_IDNA_VARIANT_2003 => Consider replace to INTL_IDNA_VARIANT_UTS46.

Notes:
1. Usage piet() (@call_with_passing_by_reference): Call with passing by reference is deprecated. Problem is "&$hoho"
2. Usage preg_replace() (@preg_replace_e_modifier): Usage of "e" modifier in preg_replace is deprecated: "asdasdsd~ie"
3. Usage password_hash() (@password_hash_salt_option): "salt" option is not secure and deprecated now
4. Usage mb_ereg_replace() (@mb_ereg_replace_e_modifier): Usage of "e" modifier in mb_ereg_replace is deprecated: ""msre""
5. Usage assert() (@assert_on_string): You should avoid using string code: "'false'"
6. Usage parse_str() (@parse_str_without_argument): Call to parse_str() without second argument is deprecated
7. Usage define() (@define_case_insensitive): Case-insensitive flag of define() is deprecated, use original constant name in your code
Peak memory usage: 1.928 MB

```

# Installation

## Phar

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
