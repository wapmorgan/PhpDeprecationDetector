<?php
class fn
{
    public function __construct()
    {
        $var = 123.2;
        var_dump(is_real($var));
        ini_set('allow_url_include', true);
    }
}

/*
 *  @See https://www.php.net/manual/en/migration74.deprecated.php
 */
// Deprecated: Array and string offset access using curly braces.
$var{$idx};
