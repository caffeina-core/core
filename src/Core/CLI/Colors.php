<?php

/**
 * CLI/Colors
 *
 * Define ANSI Colors.
 *
 * @package core
 *
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core\CLI;

class Colors {
  const ANSI = [
    "/"               => "0", // RESET
    "B"               => "1", // BOLD
    "I"               => "3", // ITALIC
    "U"               => "4", // UNDERLINE
    "S"               => "9", // STRIKED
    "K"               => "5", // BLINK
    "X"               => "7", // INVERSE
    "H"               => "8", // HIDDEN
    "BLACK"           => "30",
    "RED"             => "31",
    "GREEN"           => "32",
    "YELLOW"          => "33",
    "BLUE"            => "34",
    "MAGENTA"         => "35",
    "CYAN"            => "36",
    "GRAY"            => "37",
    "BLACK_BG"        => "40",
    "RED_BG"          => "41",
    "GREEN_BG"        => "42",
    "YELLOW_BG"       => "43",
    "BLUE_BG"         => "44",
    "MAGENTA_BG"      => "45",
    "CYAN_BG"         => "46",
    "WHITE_BG"        => "47",
    "LIGHTGRAY_BG"    => "1;40",
    "LIGHTRED_BG"     => "1;41",
    "LIGHTGREEN_BG"   => "1;42",
    "LIGHTYELLOW_BG"  => "1;43",
    "LIGHTBLUE_BG"    => "1;44",
    "LIGHTMAGENTA_BG" => "1;45",
    "LIGHTCYAN_BG"    => "1;46",
    "WHITE_BG"        => "1;47",
    "LIGHTGRAY"       => "1;30",
    "LIGHTRED"        => "1;31",
    "LIGHTGREEN"      => "1;32",
    "LIGHTYELLOW"     => "1;33",
    "LIGHTBLUE"       => "1;34",
    "LIGHTMAGENTA"    => "1;35",
    "LIGHTCYAN"       => "1;36",
    "WHITE"           => "1;37",
  ];

  protected static $styles = [];

  public static function set($name, $style) {
    return static::$styles[$name] = static::get($style);
  }

  public static function get($color) {
    if (false === $color) {
      return "\033[0m";
    }

    if (empty($color)) {
      return '';
    }

    // Precalculated Styles
    if (isset(static::$styles[$color])) {
      return static::$styles[$color];
    }

    // Render ANSI
    $code = '';
    foreach (explode('+', strtoupper(strtr($color, ' ', ''))) as $attribute) {
      if (isset(static::ANSI[$attribute])) {
        $code .= "\033[" . static::ANSI[$attribute] . "m";
      }
    }

    return $code;
  }

  public static function colorize($text) {
    $last_color = ['/'];
    return preg_replace_callback("/<([^>]+)>/", function ($m) use (&$last_color) {
      if ('/' !== $m[1][0]) {
        if (isset($m[1][2])) {
          $last_color[] = $m[1];
        }
        // is a color
        return static::get($m[1]);
      } else {
        return static::get('/') . (empty($last_color) ? '' : static::get(array_pop($last_color)));
      }
    }, "$text<normal>");
  }

}