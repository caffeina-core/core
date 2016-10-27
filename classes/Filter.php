<?php

/**
 * Filter
 *
 * Global filters handler.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2016 - http://caffeina.com
 */


// Silence -> Deprecated: Methods with the same name as their class will not be constructors in a future version of PHP
$__old_er = error_reporting();
error_reporting(E_ALL & ~E_DEPRECATED);

class Filter {
    use Module,
        Filters {
          filter       as add;
          filterSingle as single;
          filterRemove as remove;
          filterWith   as with;
    }
}

// Restore old ErrorReporting
error_reporting($__old_er);
