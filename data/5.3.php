<?php
return [
    'functions' => [
        'call_user_method' => 'call_user_func',
        'call_user_method_array' => 'call_user_func_array',
        'define_syslog_variables',
        'dl',
        'ereg' => 'preg_match',
        'ereg_replace' => 'preg_replace',
        'eregi' => 'preg_match',
        'eregi_replace' => 'preg_replace',
        'set_magic_quotes_runtime',
        'magic_quotes_runtime',
        'session_register',
        'session_unregister',
        'session_is_registered',
        'set_socket_blocking' => 'stream_set_blocking',
        'split' => 'preg_split',
        'spliti' => 'preg_split',
        'sql_regcase',
        'mysql_db_query' => 'mysql_select_db',
        'mysql_escape_string' => 'mysql_real_escape_string',
    ],
    'ini_settings' => [
        'define_syslog_variables',
        'register_globals',
        'register_long_arrays',
        'safe_mode',
        'magic_quotes_gpc',
        'magic_quotes_runtime',
        'magic_quotes_sybase',
    ],
    'identifiers' => [
        'goto',
        'namespace',
    ],
    'functions_usage' => [
        '@call_with_passing_by_reference',
    ]
];
