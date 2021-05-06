<?php

namespace Cli;


/**
 * Class CliTools
 * This tool helps you create
 */
class CliTools
{

    /**
     * Black text
     */
    const FOREGROUND_BLACK = '0;30';

    /**
     * Dark Gray Text
     */
    const FOREGROUND_DARK_GRAY = '1;30';

    /**
     * Blue Text
     */
    const FOREGROUND_BLUE = '0;34';

    /**
     * Light Blue Text
     */
    const FOREGROUND_LIGHT_BLUE = '1;3';

    /**
     * Green Text
     */
    const FOREGROUND_GREEN = '0;32';

    /**
     *Light Green Text
     */
    const FOREGROUND_LIGHT_GREEN = '1;32';

    /**
     * Cyan Text
     */
    const FOREGROUND_CYAN = '0;36';

    /**
     * Light Cyan Text
     */
    const FOREGROUND_LIGHT_CYAN = '1;36';

    /**
     * Red Text
     */
    const FOREGROUND_RED = '0;31';

    /**
     * Light Red Text
     */
    const FOREGROUND_LIGHT_RED = '1;31';

    /**
     * Purple Text
     */
    const FOREGROUND_PURPLE = '0;35';

    /**
     * Light Purple Text
     */
    const FOREGROUND_LIGHT_PURPLE = '1;35';

    /**
     * Brown Text
     */
    const FOREGROUND_BROWN = '0;33';

    /**
     * Yellow Text
     */
    const FOREGROUND_YELLOW = '1;33';

    /**
     * Light Gray Text
     */
    const FOREGROUND_LIGHT_GRAY = '0;37';

    /**
     * White Text
     */
    const FOREGROUND_WHITE = '1;37';

    /**
     * Black Background
     */
    const BACKGROUND_BLACK = '40';

    /**
     * Red Background
     */
    const BACKGROUND_RED = '41';

    /**
     * Green Background
     */
    const BACKGROUND_GREEN = '42';

    /**
     * Yellow Background
     */
    const BACKGROUND_YELLOW = '43';

    /**
     * Blue Background
     */
    const BACKGROUND_BLUE = '4';

    /**
     * Magenta Background
     */
    const BACKGROUND_MAGENTA = '45';

    /**
     * Cyan Background
     */
    const BACKGROUND_CYAN = '46';

    /**
     * Light Gray Background
     */
    const BACKGROUND_LIGHT_GRAY = '47';

    /**
     * @var string
     */
    private $_OS = 'linux';

    /**
     * CliTools constructor.
     * Check os version and set process name
     * @param string $process_name
     */
    public function __construct(string $process_name = 'Debug Mode')
    {

        cli_set_process_title($process_name);

        if (strncasecmp(PHP_OS, 'win', 3) === 0)
            $this->_OS = 'windows';

    }

    /**
     * Clear CLI screen
     * @return object
     */
    public function clear(): object
    {

        if (strncasecmp(PHP_OS, 'win', 3) === 0)
            popen('cls', 'w');
        else
            exec('clear');

        return $this;

    }

    /**
     * Create a new line with text
     * @param string $string
     * @param string|null $foreground_color
     * @param string|null $background_color
     * @param bool $replace_line
     * @return object
     */
    public function writeLine(string $string, string $foreground_color = null, string $background_color = null, bool $replace_line = FALSE): object
    {
        if ($replace_line)
            $colored_string = "\r ";
        else
            $colored_string = "";

        $colored_string .= "\033[" . $foreground_color . "m";
        $colored_string .= "\033[" . $background_color . "m";
        $colored_string .= $string . "\033[0m";

        echo $colored_string;

        return $this;
    }

    /**
     * Skip selected amount of lines
     * @param int $lines
     * @return object
     */
    public function jumpLine($lines = 1): object
    {

        for ($i = 0; $i != $lines; $i++)
            echo "\n";

        return $this;

    }

    /**
     * For debugging, you can dump var on your code
     * @param $var_to_dump
     */
    public function createBreakPoint($var_to_dump): void
    {

        var_dump($var_to_dump);

        $this::writeLine('Break point, press enter to continue...', 'red');

        $handle = fopen("php://stdin", "r");

        $line = fgets($handle);

        fclose($handle);

    }

    /**
     * Request a input from user CLI
     * @param string $message
     * @param null $foreground_color
     * @param string|null $background_color
     * @return string
     */
    public function getUserInput(string $message, $foreground_color = null, string $background_color = null): string
    {

        $this->writeLine($message, $foreground_color,$background_color);

        $handle = fopen("php://stdin", "r");

        $line = fgets($handle);

        fclose($handle);

        return trim($line);

    }

    /**
     * This method is responsible to get user cli width
     * @return int
     */
    private function getCols(): int
    {

        preg_match('/CON.*:(\n[^|]+?){3}(?<cols>\d+)/', `mode`, $matches);

        return $matches['cols'];

    }

    /**
     * @param int $Lines
     * @return object
     */
    public function newBlock($Lines = 1): object
    {

        $CliWidth = $this->getCols();
        $CliWidth = $CliWidth * $Lines;

        for ($x = 1; $x <= $CliWidth; $x++) {
            echo "\xE2\x96\x88";
        }

        return $this;

    }

    /**
     * https://stackoverflow.com/questions/2124195/command-line-progress-bar-in-php
     * Thanks bro
     * @param $done
     * @param $total
     * @param int $size
     */
    public function cliSetProgress($done, $total, $size = 30): void
    {

        static $start_time;

        // if we go over our bound, just ignore it
        if ($done > $total) return;

        if (empty($start_time)) $start_time = time();
        $now = time();

        $perc = (double)($done / $total);

        $bar = floor($perc * $size);

        $status_bar = "\r[";
        $status_bar .= str_repeat("\xE2\x96\x88", $bar);
        if ($bar < $size) {
            $status_bar .= str_repeat(" ", $size - $bar);
        } else {
            $status_bar .= "\xE2\x96\x88";
        }

        $disp = number_format($perc * 100, 0);

        $status_bar .= "] $disp%  $done/$total";

        $rate = ($now - $start_time) / $done;
        $left = $total - $done;
        $eta = round($rate * $left, 2);

        $elapsed = $now - $start_time;

        $status_bar .= " remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

        $this::cliWriteLine("$status_bar  ", 'blue');

        $my_file = __DIR__ . '\file.txt';
        $handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

        fwrite($handle, $status_bar);

        flush();

        // when done, send a newline
        if ($done == $total) {
            echo "\n\r";
        }

    }

    /**
     * Simulates typing effect..do know why...but...just go on with it.
     * @param string $Phrase
     * @return object
     */
    public function writeLineTypeEffect(string $Phrase): object
    {

        $explode_phrase = str_split($Phrase, 1);

        foreach ($explode_phrase as $letter) {

            $this->writeLine($letter);

            usleep(rand(90000, 100000));
        }

        return $this;

    }

    /**
     * Example of drawing logo (In a very bad way)
     * @return object
     */
    public function drawLogo(): object
    {

        $this->writeLine('
  ______   __        ______        ________   ______    ______   __        ______
 /      \ |  \      |      \      |        \ /      \  /      \ |  \      /      \
|  $$$$$$\| $$       \$$$$$$       \$$$$$$$$|  $$$$$$\|  $$$$$$\| $$     |  $$$$$$\
| $$   \$$| $$        | $$           | $$   | $$  | $$| $$  | $$| $$     | $$___\$$
| $$      | $$        | $$           | $$   | $$  | $$| $$  | $$| $$      \$$    \
| $$   __ | $$        | $$           | $$   | $$  | $$| $$  | $$| $$      _\$$$$$$\
| $$__/  \| $$_____  _| $$_          | $$   | $$__/ $$| $$__/ $$| $$_____|  \__| $$
 \$$    $$| $$     \|   $$ \         | $$    \$$    $$ \$$    $$| $$     \\$$    $$
  \$$$$$$  \$$$$$$$$ \$$$$$$          \$$     \$$$$$$   \$$$$$$  \$$$$$$$$ \$$$$$$');


        $this->jumpLine(1);

        return $this;
    }

}

