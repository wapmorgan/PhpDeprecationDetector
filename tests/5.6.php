<?php
// using deprecated variable
var_dump($HTTP_RAW_POST_DATA);

// setting ini
ini_set('mbstring.http_output', 'koi8-r');
