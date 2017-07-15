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

  /**
   * @return void
   */
  public function clear($fromtop=true){
    fwrite($this->stream, $fromtop ? "\033[2J\033[H" : "\r\033[0J");
  }

  /**
   * @return void
   */
  public function move($line,$col){
    fwrite($this->stream, "\033[{$line};{$col}f");
  }

  /**
   * @return void
   */
  public function top(){
    fwrite($this->stream,"\033[H");
  }

  /**
   * @return void
   */
  public function start(){
    fwrite($this->stream,"\r");
  }

  /**
   * @return void
   */
  public function up($rows){
    fwrite($this->stream,"\033[{$rows}A");
  }

  /**
   * @return void
   */
  public function down($rows){
    fwrite($this->stream, "\033[{$rows}B");
  }

  /**
   * @return void
   */
  public function right($cols){
    fwrite($this->stream, "\033[{$cols}C");
  }

  /**
   * @return void
   */
  public function left($cols){
    fwrite($this->stream, "\033[{$cols}D");
  }

  /**
   * @return void
   */
  public function save(){
    fwrite($this->stream, "\033[s");
  }

  /**
   * @return void
   */
  public function restore(){
    fwrite($this->stream, "\033[u");
  }

  /**
   * @return void
   */
  public function erase(){
    fwrite($this->stream, "\r\033[2K");
  }

  /**
   * @return void
   */
  public function hide(){
    fwrite($this->stream, "\033[?25l");
  }

  /**
   * @return void
   */
  public function show(){
    fwrite($this->stream, "\033[?25h\033[?0c");
  }

}