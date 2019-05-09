<?php
$a = create_function('', '');
ini_set('mbstring.func_overload', true);

assert('false');

read_exif_data();

echo INTL_IDNA_VARIANT_2003;

parse_str('sad=asd', $output);
parse_str('sad=asd');

list($key, $value) = each ($output);
