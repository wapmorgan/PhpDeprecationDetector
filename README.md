# PhpCodeFixer

PhpCodeFixer finds usage of deprecated functions, variables and ini directives in your php code.

# Example of usage
To scan you files or folder launch `phpcf` and pass file or directory names.
```
> php bin\phpcf tests
Scanning tests ...
[5.3] Function dl is deprecated in file tests/5.3.php[2].
[5.3] Ini setting define_syslog_variables is deprecated in file tests/5.3.php[3].
[5.4] Function mcrypt_generic_end is deprecated in file tests/5.4.php[2].
[5.5] Function preg_replace usage is deprecated (@preg_replace_e_modifier) in file tests/5.5.php[2].
[5.6] Ini setting mbstring.http_output is deprecated in file tests/5.6.php[6]. Consider using default_charset instead.
[5.6] Variable $HTTP_RAW_POST_DATA is deprecated in file tests/5.6.php[3].
```

By providing additional parameter `--target` you can specify version of PHP to perform less checks.

| Option       | Action                                |
|--------------|---------------------------------------|
| --target 7.0-experimental | Use all deprecations from 5.3 to 7.0. |
| --target 5.6 | Use all deprecations from 5.3 to 5.6. |
| --target 5.5 | Use all deprecations from 5.3 to 5.5. |
| --target 5.4 | Use all deprecations from 5.3 to 5.4. |
| --target 5.3 | Use deprecations from 5.3 only.       |

# Installation
## Composer
The recommended way to install phpcf is via composer.

1. If you do not have composer installed, download the [`composer.phar`](https://getcomposer.org/composer.phar) executable or use the installer.
  ``` sh
  $ curl -sS https://getcomposer.org/installer | php
  ```

2. Set `minimum-stability` option in composer.json to "dev".
  ``` json
  {
    "minimum-stability": "dev"
  }
  ```

3. Run `php composer.phar require wapmorgan/php-code-fixer dev-master` or add requirement in composer.json.
  ``` json
  {
    "require": {
      "wapmorgan/php-code-fixer": "dev-master"
    }
  }
  ```

4. Run `php composer.phar update`

# Composer configuration
To install phpcf and its dependencies `minimum-stability` in your composer.json should be set to `dev`.
