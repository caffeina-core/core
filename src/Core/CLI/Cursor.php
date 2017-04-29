<?php

/**
* CLI/Cursor
*
* Handles the terminal ANSI console cursor.
*
* @package core
* @author stefano.azzolini@caffeina.com
* @copyright Caffeina srl - 2015-2017 - http://caffeina.com
*/

namespace Core\CLI;

class Cursor {
  protected $stream;

  public function __construct($stream=STDOUT) {
    $this->stream = $stream;
  }

  public function cols(){
    return (int)`tput cols`;
  }

  public function rows(){
    return (int)`tput lines`;
  }

  public function clear($fromtop=true){
    fwrite($this->stream, $fromtop ? "\033[2J\033[H" : "\r\033[0J");
  }

  public function move($line,$col){
    fwrite($this->stream, "\033[{$line};{$col}f");
  }

  public function top(){
    fwrite($this->stream,"\033[H");
  }

  public function start(){
    fwrite($this->stream,"\r");
  }

  public function up($rows){
    fwrite($this->stream,"\033[{$rows}A");
  }

  public function down($rows){
    fwrite($this->stream, "\033[{$rows}B");
  }

  public function right($cols){
    fwrite($this->stream, "\033[{$cols}C");
  }

  public function left($cols){
    fwrite($this->stream, "\033[{$cols}D");
  }

  public function save(){
    fwrite($this->stream, "\033[s");
  }

  public function restore(){
    fwrite($this->stream, "\033[u");
  }

  public function erase(){
    fwrite($this->stream, "\r\033[2K");
  }

  public function hide(){
    fwrite($this->stream, "\033[?25l");
  }

  public function show(){
    fwrite($this->stream, "\033[?25h\033[?0c");
  }

}