<?php
return array(
    'functions' => array(
        'mcrypt_cbc',
        'mcrypt_cfb',
        'mcrypt_ecb',
        'mcrypt_ofb',
        'datefmt_set_timezone_id' => 'datefmt_set_timezone',
        'mysql_connect' => 'mysqli::__construct',
    ),
    'functions_usage' => array(
        'preg_replace' => '@preg_replace_e_modifier',
    ),
);