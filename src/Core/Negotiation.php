<?php

/**
 * Negotiation
 *
 * A module for handling Content Negotiation.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace Core;

class Negotiation {
  protected $list;

  /**
   * @return       array
   * @psalm-return array<int, mixed>
   */
  public static function parse($query){
    $list = new \SplPriorityQueue();
    array_map(function($e) use ($list) {
      preg_match_all('(([^;]+)(?=\s*;\s*(\w+)\s*=\s*([^;]+))*)',$e,$p);
      $params = array_map('trim',array_merge(
        [ 'type' => current($p[0]) ], array_combine($p[2], $p[3]))
      );
      unset($params['']);
      $params['q'] = isset($params['q']) ? 1.0*$params['q'] : $params['q'] = 1.0;
      $list->insert($params, $params['q']);
    },preg_split('(\s*,\s*)', $query));
    return array_values(iterator_to_array($list));
  }

  /**
   * @return false|string
   */
  public static function bestMatch($acceptables, $choices) {
    return (new self($acceptables))->best($choices);
  }

  public function __construct($query) {
    $this->list = self::parse(trim($query));
  }

  /**
   * @return string
   */
  public function preferred(){
    return self::encodeParsedValue(current($this->list));
  }

  /**
   * @return string
   */
  protected static function encodeParsedValue($parsed){
    unset($parsed['q']);     // Hide quality key from output
    $type = $parsed['type']; // Extract type
    unset($parsed['type']);
    return implode(';', array_merge([$type], array_map(function($k,$v){
      return "$k=$v";
    }, array_keys($parsed), $parsed)));
  }

  /**
   * @return false|string
   */
  public function best($choices){
    $_choices  = self::parse(trim($choices));
    foreach ($this->list as $accept){
      foreach ($_choices as $choice){
        if (preg_match('('.strtr($accept["type"],
          [ '.' => '\.', '+' => '\+', '*' => '.+' ]
        ).')', $choice["type"])){
          return self::encodeParsedValue($choice);
        }
      }
    }
    return false;
  }

} /* End of class */

