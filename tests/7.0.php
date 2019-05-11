<?php
class test {
    function test() {

    }
}

mssql_connect();

ini_get('always_populate_raw_post_data');

ini_set('always_populate_raw_post_data', true);

echo password_hash('123', PASSWORD_BCRYPT, array('cost' => 123, 'salt' => 'SALT'));

class float {
    static public function float()
    {

    }

    static public function Int()
    {}
}
