<?php
return [
    /**
     * @see https://github.com/php/php-src/blob/PHP-8.1.6/UPGRADING
     */
    'functions' => [
        // @see CORE
        '__serialize' =>'deprecate, if you order php support both implement https://wiki.php.net/rfc/phase_out_serializable',
        '__unserialize' => 'deprecate, if you order php support both implement https://wiki.php.net/rfc/phase_out_serializable',

        // @seeDate
        'date_sunrise' => 'deprecate it will remove in php 9.0',
        'date_sunset' => 'deprecate it will remove in php 9.0',
        'strftime' => 'date/DateTime::format() or IntlDateFormatter::format()',
        'gmstrftime' => 'date/DateTime::format() or IntlDateFormatter::format()',

        // @see Hash
        'mhash' => 'Use the hash_*() APIs instead',
        'mhash_keygen_s2k' => 'Use the hash_*() APIs instead',
        'mhash_count' => 'Use the hash_*() APIs instead',
        'mhash_get_block_size' => 'Use the hash_*() APIs instead',

        // @see Intl
        'roll' => 'Calling IntlCalendar::roll() with bool argument is deprecated. Pass 1 and -1 instead of true and false respectively.',

        // @see Standard
        'key' => 'use with objects is deprecated',
        'current' => 'use with objects is deprecated',
        'next' => 'use with objects is deprecated',
        'prev' => 'use with objects is deprecated',
        'reset' => 'use with objects is deprecated',
        'end' => 'use with objects is deprecated',
        'strptime' => 'Deprecated. Use date_parse_from_format() or IntlDateFormatter::parse',

        // @see MySQLi
        'MYSQLI_REFRESH_REPLICA' => 'Use MYSQLI_REFRESH_SLAVE. The old constant is still available for backwards-compatibility reasons, 
        but may be deprecated/removed in the future.'
    ],
    'constants' => [
        /**
         * @see https://github.com/php/php-src/blob/PHP-8.1.6/UPGRADING
         */
        'MYSQLI_STMT_ATTR_UPDATE_MAX_LENGTH',
        'MYSQLI_STORE_RESULT_COPY_DATA',
        'FILTER_SANITIZE_STRING' => 'Deprecated',
        'FILTER_SANITIZE_STRIPPED' => 'Deprecated',
        'NIL' => 'Deprecated, Use 0 instead',
        'FILE_BINARY' => 'deprecated. They already had no effect previously.',
        'FILE_TEXT' => 'deprecated. They already had no effect previously.',
    ],
    'ini_settings' => [
        'mysqlnd.fetch_data_copy',
        'filter.default' => 'Deprecated',
        'oci8.old_oci_close_semantics' => 'Deprecated',
        'auto_detect_line_endings' => 'Deprecated',
        'log_errors_max_len', //removed
    ],
    'identifiers' => [
        'enum'
    ]
];
