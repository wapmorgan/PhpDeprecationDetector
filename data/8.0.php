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
        // @see GD
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
        'convert_cyr_string',
        'ezmlm_hash',
        'hebrevc',
        'string.strip_tags',
        'money_format',
        'restore_include_path' => 'ini_restore(\'include_path\')',
        'get_magic_quotes_gpc' => 'They always return FALSE. ',
        'get_magic_quotes_runtime' => 'They always return FALSE. ',
        //@see Zlib
        'gzgetss',
        /**
         * @see Deprecated
         */
        //Enchant
        'enchant_broker_set_dict_path',
        'enchant_broker_get_dict_path',
        'enchant_dict_add_to_personal' => 'enchant_dict_add',
        'enchant_dict_is_in_session' => 'enchant_dict_is_added',
        'enchant_broker_free' => 'unset',
        'enchant_broker_free_dict' => 'unset',
        //@see LibXML
        'libxml_disable_entity_loader',
        //@see Reflection
        'getClass' => 'getType() or other Reflection API',
        'isDisabled' => 'getType() or other Reflection API',
        'isArray' => 'getType() or other Reflection API',
        'isCallable' => 'getType() or other Reflection API',

        //PostgresSQL
        'pg_errormessage' => 'pg_last_error',
        'pg_numrows' => 'pg_num_rows',
        'pg_numfields' => 'pg_num_fields',
        'pg_cmdtuples' => 'pg_affected_rows',
        'pg_fieldname' => 'pg_field_name',
        'pg_fieldsize' => 'pg_field_size',
        'pg_fieldtype' => 'pg_field_type',
        'pg_fieldnum' => 'pg_field_num',
        'pg_result' => 'pg_fetch_result',
        'pg_fieldprtlen' => 'pg_field_prtlen',
        'pg_fieldisnull' => 'pg_field_is_null',
        'pg_freeresult' => 'pg_free_result',
        'pg_getlastoid' => 'pg_last_oid',
        'pg_locreate' => 'pg_lo_create',
        'pg_lounlink' => 'pg_lo_unlink',
        'pg_loopen' => 'pg_lo_open',
        'pg_loclose' => 'pg_lo_close',
        'pg_loread' => 'pg_lo_read',
        'pg_lowrite' => 'pg_lo_write',
        'pg_loreadall' => 'pg_lo_read_all',
        'pg_loimport' => 'pg_lo_import',
        'pg_loexport' => 'pg_lo_export',
        'pg_setclientencoding' => 'pg_set_client_encoding',
        'pg_clientencoding' => 'pg_client_encoding',
        //Zip
        'zip_close' => 'ZipArchive',
        'zip_entry_close' => 'ZipArchive',
        'zip_entry_compressedsize' => 'ZipArchive',
        'zip_entry_compressionmethod' => 'ZipArchive',
        'zip_entry_filesize' => 'ZipArchive',
        'zip_entry_name' => 'ZipArchive',
        'zip_entry_open' => 'ZipArchive',
        'zip_entry_read' => 'ZipArchive',
        'zip_open' => 'ZipArchive',
        'zip_read' => 'ZipArchive'

//        'ReflectionFunction::isDisabled',
//        'ReflectionParameter::getClass',
//        'ReflectionParameter::isArray',
//        'ReflectionParameter::isCallable',
    ],
    'constants' => [
        /**
         * @see https://github.com/php/php-src/blob/PHP-8.0/UPGRADING
         */
        'ASSERT_QUIET_EVAL',
        'ENCHANT_MYSPELL',
        'ENCHANT_ISPELL',
        'FILTER_SANITIZE_MAGIC_QUOTES',
        'INTL_IDNA_VARIANT_2003' => 'INTL_IDNA_VARIANT_UTS46',
        'MB_OVERLOAD_MAIL',
        'MB_OVERLOAD_STRING',
        'MB_OVERLOAD_REGEX',
        'OPSYS_Z_CPM' => 'OPSYS_CPM',
        'PGSQL_LIBPQ_VERSION_STR' => 'PGSQL_LIBPQ_VERSION',
    ],
    'ini_settings' => [
        'track_errors ',
        'pdo_odbc.db2_instance_name'
    ],
    'functions_usage' => [
//        'implode' => '@implode_param_order_check',
//        '@optional_parameter_before_required',
    ],
    'identifiers' => [
        'match',
        'mixed',
        'Stringable'
    ]
];
