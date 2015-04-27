<?php

/**
 * View\Adapter
 *
 * Core\View\Adapter Interface.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace View;

interface Adapter {
    public function __construct($path=null);
    public function exists($path);
    public function render($template,$data=[]);
    public static function addGlobal($key,$val);
    public static function addGlobals(array $defs);
}
