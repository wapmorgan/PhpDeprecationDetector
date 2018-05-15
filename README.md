# PhpCodeFixer

PhpCodeFixer - a scanner that checks compatibility of your code with new interpreter versions.

[![Composer package](http://composer.network/badge/wapmorgan/php-code-fixer)](https://packagist.org/packages/wapmorgan/php-code-fixer) [![Latest Stable Version](https://poser.pugx.org/wapmorgan/php-code-fixer/v/stable)](https://packagist.org/packages/wapmorgan/php-code-fixer) [![Total Downloads](https://poser.pugx.org/wapmorgan/php-code-fixer/downloads)](https://packagist.org/packages/wapmorgan/php-code-fixer) [![License](https://poser.pugx.org/wapmorgan/php-code-fixer/license)](https://packagist.org/packages/wapmorgan/php-code-fixer)

PhpCodeFixer finds usage of deprecated functions / variables / ini-directives / constants, usage of functions with changed behavior and usage of reserved identifiers in your php code. It literally helps you fix code that can fail after migration to newer PHP version.

1. [Usage](#usage)
2. [Example](#example)
3. [Installation](#installation)

# Usage
To scan your files or folder launch `phpcf` and pass file or directory names.

```
Usage:
    phpcf [--target VERSION] [--max-size SIZE] [--exclude NAME] [--file-extensions EXT] FILES...
    phpcf --version

Options:
  -v --version             Show version.
  -t --target VERSION      Sets target php version. [default: 7.2]
  -e --exclude NAME        Sets excluded file or directory names for scanning. If need to pass few names, join it with comma.
  -s --max-size SIZE       Sets max size of php file. If file is larger, it will be skipped. [default: 1mb]
     --file-extensions EXT Sets file extensions to be parsed. [default: php, phtml, php5]
```

- By providing additional parameter `--target` you can specify version of PHP to perform less checks. Available targets: 5.3, 5.4, 5.5, 5.6, 7.0, 7.1, 7.2. A larger version includes rules for checking from all previous.
- By providing `--exclude` paramenter you can exclude specific folders or files from analyze. For example, `--exclude vendor` will prevent checking third-party libraries.
- If your files has unusual extension, you can specify all exts by `--file-extensions` argument. By default, it uses `php`, `phtml` and `php5`.

# Example of usage
```
> bin/phpcf tests
Max file size set to: 1.000 MiB
Scanning D:\Документы\PhpCodeFixer\tests ...

Folder D:\Документы\PhpCodeFixer\tests
 PHP | File:Line                           |             Type | Issue
 5.3 | /5.3.php:2                          | function         | Function dl() is deprecated.
 5.3 | /5.3.php:3                          | ini              | Ini define_syslog_variables is deprecated.
 5.3 | /5.3.php:4                          | function_usage   | Function usage piet() (@call_with_passing_by_reference) is deprecated.
 5.4 | /5.4.php:2                          | function         | Function mcrypt_generic_end() is deprecated.
 5.4 | /5.4.php:3                          | function         | Function magic_quotes_runtime() is deprecated.
 5.5 | /5.5.php:2                          | function_usage   | Function usage preg_replace() (@preg_replace_e_modifier) is deprecated.
 5.6 | /5.6.php:6                          | ini              | Ini mbstring.http_output is deprecated.
 5.6 | /5.6.php:3                          | variable         | Variable $HTTP_RAW_POST_DATA is deprecated.
 7.0 | /7.0.php:8                          | function         | Function mssql_connect() is deprecated.
 7.0 | /7.0.php:10                         | ini              | Ini always_populate_raw_post_data is deprecated.
 7.0 | /7.0.php:12                         | function_usage   | Function usage password_hash() (@password_hash_salt_option) is deprecated.
 7.0 | /7.0.php:14                         | identifier       | Identifier float is reserved by PHP core.
 7.0 | /7.0.php:3                          | method_name      | Method name test:test (@php4_constructors) is deprecated.
 7.1 | /7.1.php:2                          | function         | Function mcrypt_decrypt() is deprecated.
 7.1 | /7.1.php:4                          | ini              | Ini session.hash_function is deprecated.
 7.1 | /7.1.php:9                          | identifier       | Identifier iterable is reserved by PHP core.
 7.2 | /7.2.php:2                          | function         | Function create_function() is deprecated.
 7.2 | /7.2.php:7                          | function         | Function read_exif_data() is deprecated.
 7.2 | /7.2.php:9                          | constant         | Constant INTL_IDNA_VARIANT_2003 is deprecated.
 7.2 | /7.2.php:3                          | ini              | Ini mbstring.func_overload is deprecated.
 7.2 | /7.2.php:5                          | function_usage   | Function usage assert() (@assert_on_string) is deprecated.
 7.2 | /7.2.php:12                         | function_usage   | Function usage parse_str() (@parse_str_without_argument) is deprecated.

Total problems: 22

Replace Suggestions:
1. Don't use function mcrypt_generic_end() => Consider replace to mcrypt_generic_deinit().
2. Don't use function read_exif_data() => Consider replace to exif_read_data().
3. Don't use ini mbstring.http_output => Consider replace to default_charset.
4. Don't use variable $HTTP_RAW_POST_DATA => Consider replace to php://input.
5. Don't use constant INTL_IDNA_VARIANT_2003 => Consider replace to INTL_IDNA_VARIANT_UTS46.

Notes:
1. Usage piet() (@call_with_passing_by_reference): Call with passing by reference is deprecated. Problem is "&$hoho"
2. Usage preg_replace() (@preg_replace_e_modifier): Usage of "e" modifier in preg_replace is deprecated: "asdasdsd~ie"
3. Usage password_hash() (@password_hash_salt_option): "salt" option is not secure and deprecated now
4. Usage assert() (@assert_on_string): You should avoid using string code: "'false'"
5. Usage parse_str() (@parse_str_without_argument): Call to parse_str() without second argument is deprecated
Peak memory usage: 1.135 MB
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
