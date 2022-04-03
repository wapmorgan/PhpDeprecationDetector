<?php
pg_errormessage();

echo ENCHANT_MYSPELL;

/*
 *  @See https://www.php.net/manual/en/language.oop5.decon.php
 */
// In namespaced classes, or any class as of PHP 8.0.0, a method named the same as the class never has any special meaning.
class Point {
    protected int $x;
    protected int $y;

    public function Point(int $x, int $y = 0) {
        $this->x = $x;
        $this->y = $y;
    }
}

$p1 = new Point(4, 5);

/*
 *  @See https://www.php.net/manual/en/function.implode
 */
// Passing the separator after the array is no longer supported.
$testArray = [1,2,3,4];
$result = implode($testArray, "|");
