<?php

/**
* CLI/UI/ProgressBar
*
* Renders a dynamic progress bar on the terminal.
*
* @package core
* @author stefano.azzolini@caffeina.com
* @copyright Caffeina srl - 2015-2017 - http://caffeina.com
*/

namespace Core\CLI\UI;

class ProgressBar implements Drawable {

  protected $max   = 100,
            $value = 0,
            $cols  = 77,
            $color = '',
            $label = '',
            $nl    = true,
            $_open = '[', $_close = ']', $_empty = ' ', $_full = '=';

  public function __construct( $label,
                               $max=100,
                               $color=null,
                               $style=null,
                               $nl=true
                             ){
    $this->label  = $label;
    $this->max    = $max;
    $this->nl     = $nl;
    $this->cols   = (int)`tput cols`-3 ?: 77;
    $this->color  = \Core\CLI::color($color ?: 'lightgreen');
    if ($style) list(
      $this->_open,
      $this->_full,
      $this->_empty,
      $this->_close
    ) = str_split(str_pad($style, 4, " "));
  }

  public function width($cols=null){
    return $cols === null ? $this->cols : $this->cols = $cols;
  }

  public function value($x=null){
    return $x === null ? $this->value : $this->value = $x;
  }

  public function inc($x=1){
    return $this->value = min($this->max, $this->value + $x);
  }

  public function set($value){
    return $this->value = max(-1,min($this->max, $value));
  }

  public function label($text=null){
    return $text === null ? $this->label : $this->label = $text;
  }

  public function draw(){
    $L = strlen($this->label);
    $X = $this->value / $this->max;
    $C = $this->cols - 9 -($L ? $L+1 : 0);
    $P = $this->color
         . str_repeat($this->_full,  $W = round($C * $X) ) . "\033[0;30m"
         . str_repeat($this->_empty, max($C - $W, 0))      . "\033[0m";

    echo "\033[0m" , ($L ? $this->label . ' ' : ''),
         "{$this->_open}{$P}{$this->_close} ",
         str_pad(number_format($X*100,1),5," ",STR_PAD_LEFT),
         "%", ($this->nl ? "\n" : '');
  }

}
