<?php
return [
    'functions' => [
        /**
         * @see http://php.net/manual/en/migration73.deprecated.php#migration73.deprecated.image
         */
        'image2wbmp' => 'imagewbmp',

        /**
         * @see http://php.net/manual/en/migration73.deprecated.php#migration73.deprecated.core.strip-tags-streaming
         */
        'fgetss',

        /**
         * @see http://php.net/manual/en/migration73.deprecated.php#migration73.deprecated.mbstring
         */
        'mbregex_encoding',
        'mbereg',
        'mberegi',
        'mbereg_replace',
        'mberegi_replace',
        'mbsplit',
        'mbereg_match',
        'mbereg_search',
        'mbereg_search_pos',
        'mbereg_search_regs',
        'mbereg_search_init',
        'mbereg_search_getregs',
        'mbereg_search_getpos',
        'mbereg_search_setpos',
    ],
    'constants' => [
        /**
         * @see http://php.net/manual/en/migration73.deprecated.php#migration73.deprecated.filter
         */
        'FILTER_FLAG_SCHEME_REQUIRED',
        'FILTER_FLAG_HOST_REQUIRED',
    ],
    'ini_settings' => [
        /**
         * @see http://php.net/manual/en/migration73.deprecated.php#migration73.deprecated.pdo-odbc
         */
        'pdo_odbc.db2_instance_name',
    ],
    'functions_usage' => [
        /**
         * @see http://php.net/manual/en/migration73.deprecated.php#migration73.deprecated.core.ci-constant
         */
        'define' => '@define_case_insensitive',
    ],
];
