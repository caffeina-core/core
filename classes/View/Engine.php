<?php

/**
 * View\Engine
 *
 * Core\View\Engine template interface.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

namespace View;

interface Engine {
    public function exists($path);
    public function render($template,$data=[]);
    public static function addGlobal($key,$val=null);
}

