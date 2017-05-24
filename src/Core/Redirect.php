<?php

/**
 * Redirect
 *
 * HTTP redirection commands.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

namespace Core;

class Redirect {
    use Module;

    public static function to($url, $status=302){
        if ($link = Filter::with('core.redirect',$url)) {
          Response::clean();
          Response::status($status);
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
        Response::add('<script>'.($parent?'parent.':'').'location.href="'.addslashes($link).'"</script>');
        Response::send();
        exit;
      }
    }

}
