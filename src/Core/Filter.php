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

namespace Core;

abstract class Filter {
    use Module,
        Filters {
          filter       as add;
          filterSingle as single;
          filterRemove as remove;
          filterWith   as with;
    }
}

