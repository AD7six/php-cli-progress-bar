<?php
/**
 * PHP CLI Progress bar
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Andy Dawson
 * @link          http://ad7six.com
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * ProgressBar 
 *
 * Static wrapper class for generating progress bars for cli tasks
 * 
 */
class ProgressBar
{

    /**
     * Merged with options passed in start function 
     */
    protected static $defaults = array(
        'format' => "\r:message::padding:%.01f%% %2\$d/%3\$d ETC: %4\$s. Elapsed: %5\$s [%6\$s]",
        'message' => 'Running',
        'size' => 30,
        'width' => null
    );

    /**
     * How much have we done already 
     */
    protected static $done = 0;

    /**
     * The format string 
     */
    protected static $format;

    /**
     * message to display prefixing the progress bar
     */
    protected static $message;

    /**
     * How many chars to use for the progress bar 
     */
    protected static $size = 30;

    /**
     * When did we start 
     */
    protected static $start;

    /**
     * The width in characters the whole rendered string must fit in. defaults to the width of the 
     * terminal window
     */
    protected static $width;

    /**
     * What's the total number of times we're going to call set 
     */
    protected static $total;

    /**
     * Show a progress bar, allowing to override the total or size (for this and subsequent bars)
     * 
     * @param int $done what fraction of $total to set as progress
     * 
     * @static
     * @return void
     */
    public static function display($done = null)
    {
        if ($done) {
            self::$done = $done;
        }

        $now = time();

        if (self::$total) {
            $fractionComplete = (double) (self::$done / self::$total);
        } else {
            $fractionComplete = 0;
        }

        $bar = floor($fractionComplete * self::$size);
        $barSize = min($bar, self::$size);

        $barContents = str_repeat('=', $barSize);
        if ($bar < self::$size) {
            $barContents .= '>';
            $barContents .= str_repeat(' ', self::$size - $barSize);
        } elseif ($fractionComplete > 1) {
            $barContents .= '!';
        } else {
            $barContents .= '=';
        }

        $percent = number_format($fractionComplete * 100, 0);

        $elapsed = $now - self::$start;
        if (self::$done) {
            $rate = $elapsed / self::$done;
        } else {
            $rate = 0;
        }
        $left = self::$total - self::$done;
        $etc = round($rate * $left, 2);

        if (self::$done) {
            $etcNowText = '< 1 sec';
        } else {
            $etcNowText = '???';
        }
        $timeRemaining = self::humanTime($etc, $etcNowText);
        $timeElapsed = self::humanTime($elapsed);

        $return = sprintf(
            self::$format,
            $percent,
            self::$done,
            self::$total,
            $timeRemaining,
            $timeElapsed,
            $barContents
        );

        $width = strlen(preg_replace('@(?:\r|:\w+:)@', '', $return));

        if (strlen(self::$message) > (self::$width - $width - 3)) {
            $message = substr(self::$message, 0, (self::$width - $width - 4)) . '...';
            $padding = '';
            echo "\n" . strlen($return);
        } else {
            $message = self::$message;
            $width += strlen($message);
            $padding = str_repeat(' ', (self::$width - $width));
        }

        $return = str_replace(':message:', $message, $return);
        $return = str_replace(':padding:', $padding, $return);

        return $return;
    }

    /**
     * finish 
     * 
     * @static
     * @return string, a new line
     */
    public static function finish()
    {
        self::start();
        return "\n";
    }

    /**
     * change the message  used without calling set
     * 
     * @param string $message the string to display with the progress bar
     *
     * @static
     * @return void
     */
    public static function message($message = '')
    {
        self::$message = $message;
    }

    /**
     * Increment the internal counter, and returns the result of set
     * 
     * @param int    $inc     Amount to increment the internal counter
     * @param string $message If passed, overrides the existing message
     *
     * @static
     * @return void
     */
    public static function next($inc = 1, $message = '')
    {
        self::$done += $inc;

        if ($message) {
            self::$message = $message;
        }

        return self::display();
    }

    /**
     * start 
     *
     * Initialize a bar, resets the start timer and the size
     * 
     * @param mixed $total   number of times we're going to call set
     * @param int   $message message to prefix the bar with
     * @param int   $options overrides for default options
     * 
     * @static
     * @return void
     */
    public static function start($total = null, $message = '', $options = array())
    {
        if ($message) {
            $options['message'] = $message;
        }
        $options = array_merge(self::$defaults, $options);

        self::$done = 0;
        self::$format = $options['format'];
        self::$message = $options['message'];
        self::$size = $options['size'];
        self::$start = time();
        self::$total = $total;

        self::setWidth($options['width']);

        return self::next(0);
    }

    /**
     * humanTime 
     *
     * Convert a number of seconds into something human readable like "2 days, 4 hrs"
     * 
     * @param int    $seconds how far in the future/past to display
     * @param string $nowText if there are no seconds, what text to display
     *
     * @static
     * @return string representation of the time
     */
    protected static function humanTime($seconds, $nowText = '< 1 sec')
    {
        $prefix = '';
        if ($seconds < 0) {
            $prefix = '- ';
            $seconds = -$seconds;
        }

        $days = $hours = $minutes = 0;

        if ($seconds >= 86400) {
            $days = (int) ($seconds / 86400);
            $seconds = $seconds - $days * 86400;
        }
        if ($seconds >= 3600) {
            $hours = (int) ($seconds / 3600);
            $seconds = $seconds - $hours * 3600;
        }
        if ($seconds >= 60) {
            $minutes = (int) ($seconds / 60);
            $seconds = $seconds - $minutes * 60;
        }
        $seconds = (int) $seconds;

        $return = array();

        if ($days) {
            $return[] = "$days days";
        }
        if ($hours) {
            $return[] = "$hours hrs";
        }
        if ($minutes) {
            $return[] = "$minutes mins";
        }
        if ($seconds) {
            $return[] = "$seconds secs";
        }

        if (!$return) {
            return $nowText;
        }
        return $prefix . implode(array_slice($return, 0, 2), ', ');
    }

    /**
     * If not set explicitly use the full width of the terminal window
     * 
     * @param int $width passed in options
     *
     * @static
     * @return void
     */
    protected static function setWidth($width = null)
    {
        if ($width === null) {
            if (DIRECTORY_SEPARATOR === '/') {
                $width = `tput cols`;
            }
            if ($width < 80) {
                $width = 80;
            }
        }
        self::$width = $width;
    }
}
