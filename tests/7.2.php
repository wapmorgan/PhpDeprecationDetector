<?php
$a = create_function('', '');
ini_set('mbstring.func_overload', true);

assert('false');

read_exif_data();

parse_str('sad=asd', $output);
parse_str('sad=asd');
