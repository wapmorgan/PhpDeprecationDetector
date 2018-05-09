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
     * Checks that output is a terminal, not a file|pipe
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
     * Checks that terminal supports colors
     * @return bool
     */
    static public function isColorsCapable()
    {
        if (self::$colorsCapability === null) {
            if (!static::isUnixPlatform())
                self::$colorsCapability = false;
            else if (!static::isInteractive())
                self::$colorsCapability = false;
            else {
                $tput_presence = static::exec('which tput');
                if (strlen(trim($tput_presence[0])) === 0)
                    self::$colorsCapability = false;
                else {
                    $tput_colors = static::exec('tput colors');
                    self::$colorsCapability = (int)$tput_colors[0] > 0;
                }
            }
        }
        return self::$colorsCapability;
    }

    /**
     * Outputs text with background/foreground color
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
     * Added prefix for color and postfix for color reset
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
     * Returns width (columns) of terminal
     * @return int
     */
    static public function getWidth() {
        if (static::$size === null)
            static::getSize();

        return static::$size[1];
    }

    /**
     * Returns height (rows) of terminal
     * @return int
     */
    static public function getHeight() {
        if (static::$size === null)
            static::getSize();

        return static::$size[0];
    }

    /**
     * Returns size of terminal on Windows
     * @return array
     */
    static protected function getWindowsTerminalSize() {
        $output = self::exec('mode', $returnCode);
        if ($returnCode !== 0)
            return [25, 80];

        foreach ($output as $i => $line) {
            if (strpos($line, ' CON') !== false) {
                $sizes = [$output[$i + 2], $output[$i + 3]];
                break;
            }
        }
        if (!isset($sizes))
            return [25, 80];

        return array_map(function($val) { list(, $val) = explode(':', $val); return trim($val); }, $sizes);
    }

    /**
     * Returns size of terminal on Unix
     * @return array
     */
    static protected function getUnixTerminalSize() {
        $out = self::exec('stty size', $returnCode);
        if ($returnCode !== 0)
            return [25, 80];
        return array_map('trim', explode(' ', $out[0]));
    }

    /**
     * Executes system command and returns output
     * @param string $cmd
     * @param null|mixed &$returnCode Exit code of command will be stored in this argument
     * @return array List of output lines
     */
    static protected function exec($cmd, &$returnCode = null)
    {
        \exec($cmd, $output, $returnCode);
        return $output;
    }

    /**
     * Gets size of terminal
     */
    static protected function getSize()
    {
        if (static::isUnixPlatform()) {
            static::$size = static::getUnixTerminalSize();
        } else {
            static::$size = static::getWindowsTerminalSize();
        }
    }

    /**
     * Checks that running platform is unix
     * @return bool
     */
    static protected function isUnixPlatform()
    {
        return (strncasecmp(PHP_OS, 'win', 3) !== 0);
    }
}
