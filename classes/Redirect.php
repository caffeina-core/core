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
        Response::clean();
        Response::header('Location',$url);
        Response::send();
        exit;
    }

    public static function viaJavaScript($url,$parent=false){
        Response::type('text/html');
        Response::text('<script>'.($parent?'parent.':'').'location.href="',addslashes($url),'"</script>');
    }

}
