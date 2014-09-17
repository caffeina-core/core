<?php

/**
 * String
 *
 * A module of string related utility.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

class String {
  use Module;

	/**
	 * Fast string templating.
	 * Uses a Twig-like syntax.
	 *
	 * @example
	 *		echo String::render('Your IP is : {{ server.REMOTE_HOST }}',array('server' => $_SERVER));
	 *
	 * @author Stefano Azzolini <stefano.azzolini@caffeinalab.com>
	 * @access public
	 * @static
	 * @param mixed $t	The text template
	 * @param mixed $v (default: null)	The array of values exposed in template.
	 * @return string
	 */
	 public static function render($t,$v=null){
	   for($r=$ox=$x=false;false!==($x=$y=strpos($t,'{{',$x));
	     $r.=substr($t,$ox,$x-$ox),
	     $c=substr($t,$x+=2,$l=($y=strpos($t,'}}',$x))-$x),
	     $ox=$x+=$l+2,$r.=Object::fetch($c,$v)
	   ); return $r===false?$t:$r.substr($t,$ox);
	 }

} /* End of class */
