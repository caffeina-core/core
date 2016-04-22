<?php

/**
 * Text
 *
 * A module of string related utility.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Text {
  use Module;

  /**
   * Fast string templating.
   * Uses a Twig-like syntax.
   *
   * @example
   *    echo Text::render('Your IP is : {{ server.REMOTE_HOST }}',array('server' => $_SERVER));
   *
   * @author Stefano Azzolini <stefano.azzolini@caffeinalab.com>
   * @access public
   * @static
   * @param mixed $t  The text template
   * @param mixed $v (default: null)  The array of values exposed in template.
   * @return string
  */
  public static function render($t,$v=null){
    if (empty($v)) return preg_replace('/{{[^}]+}}/','',$t);
    for(  // Init
          $r = $ox = $x = false;
          // While
          false !== ($x = $y = strpos($t,'{{',$x));
          // Do
          $r .= substr($t, $ox, $x-$ox),
          $c  = substr($t, $x += 2, $l = ( $y = strpos($t,'}}', $x) ) - $x),
          $ox = $x += $l + 2,
          $r .= Object::fetch(trim($c),$v)?:''
    );
    return $r===false ? $t : $r.substr($t,$ox);
  }

  /**
   * Create a "slug", an url-safe sanitized string.
   *
   * @example
   *   echo Text::slugify("Thîs îs --- à vêry wrong séntènce!");
   *   // this-is-a-very-wrong-sentence
   *
   * @access public
   * @static
   * @param  string $text The text to slugify
   * @return string       The slug.
   */
  public static function slugify($text){
    return preg_replace(
      ['(\s+)','([^a-z0-9-])i','(-+)'],['-','','-'],
      strtolower(self::removeAccents($text)));
  }

  /**
   * Translit accented characters to neutral ones
   *
   * @example
   *   echo Text::removeAccents("Thîs îs à vêry wrong séntènce!");
   *   // This is a very wrong sentence!
   *
   * @access public
   * @static
   * @param  string $text The text to translit
   * @return string       The translited text
   */
  public static function removeAccents($text){
    static $diac;
    return strtr(
      utf8_decode($text),
      $diac ? $diac : $diac = utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
      'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
  }

} /* End of class */
