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
