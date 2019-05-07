<?php
define('TEST_CONST', 123, true);
filter_var('http://example.com', FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
