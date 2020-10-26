<?php
return [
     /**
        * @see https://github.com/php/php-src/blob/PHP-8.0/UPGRADING
        */
    'methods_naming' => [
        '@php4_constructors'
    ],
    'functions' => [
        // @see CORE
        '__autoload' =>'spl_autoload_register',
        'create_function' => 'Anonymous functions may be used instead.',
        'each' => 'foreach or ArrayIterator should be used instead.',
        'image2wbmp' => 'imagewbmp',
        'png2wbmp',
        'jpeg2wbmp',
       // @Exif
       'read_exif_data' => 'exif_read_data',
       // @GMP
       'gmp_random' => 'gmp_random_range, or  gmp_random_bits ',
       // @IMAP
       'imap_header' => 'imap_headerinfo',
        // @see LDAP
        'ldap_set_rebind_proc' => '$callback parameter does not accept empty string anymore; null value shall be used instead.',
        'ldap_sort',
        'ldap_control_paged_result',
        'ldap_control_paged_result_response',
        // @see MB
        'mb_get_info',
        'mb_parse_str' => 'can no longer be used without specifying a result array.',
        'mbregex_encoding' => 'mb_regex_encoding',
        'mbereg'  => 'mb_ereg',
        'mberegi' => 'mb_eregi',
        'mbereg_replace'    => 'mb_ereg_replace',
        'mberegi_replace'   => 'mb_eregi_replace',
        'mbsplit'   => 'mb_split',
        'mbereg_match'   =>   'mb_ereg_match',
        'mbereg_search' =>   'mb_ereg_search',
        'mbereg_search_pos' => 'mb_ereg_search_pos',
        'mbereg_search_regs'    => 'mb_ereg_search_regs',
        'mbereg_search_init'    => 'mb_ereg_search_init',
        'mbereg_search_getregs' => 'mb_ereg_search_getregs',
        'mbereg_search_getpos'  => 'mb_ereg_search_getpos',
        'mbereg_search_setpos'  => 'mb_ereg_search_setpos',
        'mb_ereg_replace' => '@mb_ereg_replace_e_modifier',
        'newInstanceArgs' => 'newInstance(...$args)',
        'fgetss',
        'push' => 'SplDoublyLinkedList::push() now returns void instead of true',
        'unshift' => 'SplDoublyLinkedList::unshift() now returns void instead of true',
        //standard
        'string.strip_tags',
        'hebrevc',
        'convert_cyr_string',
        'money_format',
        'ezmlm_hash',
        'restore_include_path' => 'ini_restore(\'include_path\')',
        'get_magic_quotes_gpc' => 'They always return FALSE. ',
        'get_magic_quotes_runtime' => 'They always return FALSE. ',
        //@see Zlib
        'gzgetss',
        /**
         * @see Deprecated
         */
        //@see LibXML
        'libxml_disable_entity_loader',
        //@see Reflection
        'isDisabled' => 'getType() or other Reflection API',
        'getClass' => 'getType() or other Reflection API',
        'isArray' => 'getType() or other Reflection API',
        'isCallable' => 'getType() or other Reflection API'
    ],
    'constants' => [
        /**
         * @see https://github.com/php/php-src/blob/PHP-8.0/UPGRADING
         */
        'INTL_IDNA_VARIANT_2003' => 'INTL_IDNA_VARIANT_UTS46',
        'MB_OVERLOAD_MAIL',
        'MB_OVERLOAD_STRING',
        'MB_OVERLOAD_REGEX',
        'OPSYS_Z_CPM' => 'OPSYS_CPM',
        'ASSERT_QUIET_EVAL',
        'PG_VERSION_STR' => 'PG_VERSION',
        'FILTER_SANITIZE_MAGIC_QUOTES'
    ],
    'ini_settings' => [
        'track_errors ',
        'pdo_odbc.db2_instance_name'
    ],
    'functions_usage' => [
        'implode' => '@implode_param_order_check'
    ],
    'identifiers' => [
        'match',
        'mixed',
        'Stringable'
    ]
];
