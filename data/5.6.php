<?php
return array(
    'variables' => array(
        '$HTTP_RAW_POST_DATA' => 'php://input',
    ),
    'ini_settings' => array(
        'iconv.input_encoding' => 'default_charset',
        'iconv.output_encoding' => 'default_charset',
        'iconv.internal_encoding' => 'default_charset',
        'mbstring.http_input' => 'default_charset',
        'mbstring.http_output' => 'default_charset',
        'mbstring.internal_encoding' => 'default_charset',
    ),
);
