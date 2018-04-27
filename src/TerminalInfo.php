<?php
namespace wapmorgan\PhpCodeFixer;

class TerminalInfo {
    const RESET_COLOR = "\e[0m";
    const ORANGE_TEXT = "\e[0;33m";
    const YELLOW_TEXT = "\e[0;93m";
    const GRAY_TEXT = "\e[0;37m";
    const RED_TEXT = "\e[0;31m";
    const GREEN_TEXT = "\e[0;32m";
    const BLUE_TEXT = "\e[0;34m";
    const WHITE_TEXT = "\e[1;97m";

    const RED_UNDERLINED_TEXT = "\e[4;31m";
    const GREEN_UNDERLINED_TEXT = "\e[4;32m";

    const RED_BACKGROUND = "\e[41m";
    const GREEN_BACKGROUND = "\e[42m";

    /** @var array Size of terminal */
    static protected $size;

    /** @var boolean Cached value of terminal colors ability */
    static protected $colorsCapability;

    /**
     * @return bool
     */
    static public function isInteractive() {
        if (strncasecmp(PHP_OS, 'win', 3) === 0) {
            // windows has no test for that
            return true;
        } else {
            if (function_exists('posix_isatty'))
                return posix_isatty(STDOUT);

            // test with fstat()
            $mode = fstat(STDIN);
            return ($mode['mode'] & 0170000) == 0020000; // charater flag (input iteractive)
        }
    }

    /**
     * @return bool
     */
    static public function isColorsCapable()
    {
        if (self::$colorsCapability === null) {
            if (!static::isUnixPlatform())
                self::$colorsCapability = false;
            else if (!static::isInteractive() || strlen(trim(static::exec('which tput'))) === 0)
                self::$colorsCapability = false;
            else
                self::$colorsCapability = (int)trim(static::exec('tput colors')) > 0;
        }
        return self::$colorsCapability;
    }

    /**
     * @param string $text
     * @param string $color
     * @param string|null $backgroundColor
     */
    static public function echoWithColor($text, $color, $backgroundColor = null) {
        if (static::isColorsCapable())
            fwrite(STDOUT, $backgroundColor . $color . $text . self::RESET_COLOR);
        else
            fwrite(STDOUT, $text);
    }

    /**
     * @param $text
     * @param $color
     * @return string
     */
    static public function colorize($text, $color) {
        if (static::isColorsCapable())
            return $color.$text.self::RESET_COLOR;
        else
            return $text;
    }

    /**
     * @return int
     */
    static public function getWidth() {
        if (static::$size === null)
            static::getSize();

        return static::$size[1];
    }

    /**
     * @return int
     */
    static public function getHeight() {
        if (static::$size === null)
            static::getSize();

        return static::$size[0];
    }

    /**
     * @return array
     */
    static protected function getWindowsTerminalSize() {
        $output = explode("\n", self::exec('mode'));
        return array_map(function($val) { list(, $val) = explode(':', $val); return trim($val); },  [$output[3], $output[4]]);
    }

    /**
     * @return array
     */
    static protected function getUnixTerminalSize() {
        $out = self::exec('stty size');
        return array_map('trim', explode(' ', $out));
    }

    static protected function exec($cmd)
    {
        return shell_exec($cmd);
    }

    static protected function getSize()
    {
        if (static::isUnixPlatform()) {
            static::$size = static::getUnixTerminalSize();
        } else {
            static::$size = static::getWindowsTerminalSize();
        }
    }

    static protected function isUnixPlatform()
    {
        return (strncasecmp(PHP_OS, 'win', 3) !== 0);
    }
}
