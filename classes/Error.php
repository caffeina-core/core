<?php

/**
 * Error
 *
 * Handle system and application errors.
 *
 * @package core
 * @deprecated Error is private in PHP7, use Errors instead
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

include_once __DIR__.'Errors.php';
class_alias('Errors','Error',true);
