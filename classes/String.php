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
		$t = is_array($t)?'':$t;
		if(empty($v)) return $t;
		for ($e=explode('{{',$t),$r=$e[$i=0];
			isset($e[++$i]) && $ss=explode('}}',$e[$i]);
			$r.=(strpos($q=trim($ss[0]),'.')!==false?
			static::_rh($q,$v):(isset($v[$q])?$v[$q]:''))
			.end($ss));return $r;
	}

	/**
	 * Fast string templating internal private accessory function.
	 *
	 * @author Stefano Azzolini <stefano.azzolini@caffeinalab.com>
	 * @access private
	 * @static
	 * @param mixed &$f
	 * @param mixed &$v
	 * @return void
	 */
	private static function _rh(&$f,&$v) {
			static $m;$m?:$m=array();
			if(isset($m[$f])) return $m[$f];
			for($t=strtok($f,'.'),$q=isset($v[$t])?$v:'';
				isset($q[$t])&&$t!==false;
				$q=isset($q[$t])?$q[$t]:'',$t=strtok('.')
		); return $m[$f]=($t?'':$q);
	}


} /* End of class */
