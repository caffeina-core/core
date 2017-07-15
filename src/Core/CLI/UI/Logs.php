<?php

/**
* CLI/UI/Logs
*
* Write a stream of log messages on the terminal.
*
* @package core
* @author stefano.azzolini@caffeina.com
* @copyright Caffeina srl - 2015-2017 - http://caffeina.com
*/

namespace Core\CLI\UI;

class Logs implements Drawable {

    protected
      $logs = [],
      $max_lines = 5,
      $styles = [
        'normal'   => ["lightgray_bg+black",   "lightgray"],
        'info'     => ["lightgray_bg+black",   "cyan"],
        'success'  => ["lightgray_bg+black",  "green"],
        'warning'  => ["yellow_bg+black", "yellow"],
        'error'    => ["red_bg+black",    "red"],
      ];

    public function __construct($max_lines=5){
      $this->max_lines = max(1, $max_lines);
      array_walk($this->styles, function(&$x){
        $x[0] = \Core\CLI::color($x[0]);
        $x[1] = \Core\CLI::color($x[1]);
      });
    }

    /**
     * @return void
     */
    public function log($type,$text){
      $type = $type ?? "normal";
      $reset = \Core\CLI::color(false);
      $c = isset($this->styles[$type]) ? $this->styles[$type] : $this->styles["normal"];
      $pre = $c[0] . "[".date("Y-m-d H:i:s",time())."]" . $reset . $c[1] . " ";
      $this->logs[] = "{$pre}$text" . $reset;
      if (count($this->logs) > $this->max_lines) array_shift($this->logs);
    }

    /**
     * @return void
     */
    public function draw(){
      $cols = (int)`tput cols`;
      for ($i = 0 ; $i < $this->max_lines ; $i++) {
        $line = isset($this->logs[$i]) ? substr(str_pad($this->logs[$i], $cols*2 ,' ', STR_PAD_RIGHT),0,$cols-1) : '';
        echo "$line\n";
      }
    }

  }
