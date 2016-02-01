<?php

/**
 * Redirect
 *
 * HTTP redirection commands.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Redirect {
    use Module;

    public static function to($url){
        if ($link = Filter::with('core.redirect',$url)) {
          Response::clean();
          Response::header('Location', $link);
          Response::send();
          exit;
        }
    }

    public static function back(){
        if ($link = Filter::with('core.redirect', (empty($_SERVER['HTTP_REFERER']) ? Request::get('redirect_uri',false) : $_SERVER['HTTP_REFERER']) )){
          Response::clean();
          Response::header('Location', $link);
          Response::send();
          exit;
        }
    }

    public static function viaJavaScript($url, $parent=false){
      if ($link = Filter::with('core.redirect', $url)){
        Response::type('text/html');
        Response::add('<script>'.($parent?'parent.':'').'location.href="',addslashes($link),'"</script>');
        Response::send();
        exit;
      }
    }

}
