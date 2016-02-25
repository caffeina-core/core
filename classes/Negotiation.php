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

class Negotiation {
  protected $list;

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

  public static function bestMatch($acceptables, $choices) {
    return (new self($acceptables))->match($choices);
  }

  public function __construct($query) {
    $this->list = self::parse(trim($query));
  }

  public function preferred(){
    return current($this->list) ?: false;
  }

  public function best($choices){
    $_choices  = self::parse(trim($choices));
    foreach ($this->list as $accept){
      foreach ($_choices as $choice){
        if (preg_match('('.strtr($accept["type"],
          [ '.' => '\.', '+' => '\+', '*' => '.+' ]
        ).')', $choice["type"])){
          unset($choice['q']);     // Hide quality key from output
          $type = $choice['type']; // Extract type
          unset($choice['type']);
          return implode(';', array_merge([$type], array_map(function($k,$v){
            return "$k=$v";
          }, array_keys($choice), $choice)));
        }
      }
    }
    return false;
  }

} /* End of class */

