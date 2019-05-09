<?php
return [
    'functions' => [
        'create_function',
        'each' => 'foreach',
        'read_exif_data' => 'exif_read_data',
        'png2wbmp',
        'jpeg2wbmp',
        'gmp_random' => 'gmp_random_range',
    ],
    'constants' => [
        'INTL_IDNA_VARIANT_2003' => 'INTL_IDNA_VARIANT_UTS46',
    ],
    'ini_settings' => [
        'mbstring.func_overload',
    ],
    'functions_usage' => [
        'parse_str' => '@parse_str_without_argument',
        'assert' => '@assert_on_string',
    ],
    'identifiers' => [
        'object',
    ],
    'variables' => [
        'php_errormsg',
    ],
];
