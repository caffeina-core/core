<?php

/**
 * REST
 *
 * REST utility and router shortcuts.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class REST {

  /**
   * Enable REST routes to expose a resource.
   * 
   * Example:
   * <code>
   *   // Create a new '/bucket' route group.
   *   REST::expose('bucket',[
   *     'create'  => function(){      echo "New bucket"; },
   *     'read'    => function($id){   echo "SHOW bucket($id)"; },
   *     'update'  => function($id){   echo "MODIFY bucket($id)"; },
   *     'delete'  => function($id){   echo "DELETE bucket($id)"; },
   *     'list'    => function(){      echo "All buckets"; },
   *     'clear'   => function(){      echo "Cleared all buckets"; },
   *   ]);
   * 
   * 
   * 
   *   Route::group('/non_conventional_/route/base',function(){
   *     // Create directly the REST routes (for use inside another parent group).
   *     REST::expose([
   *       'create'  => function(){      echo "New bucket"; },
   *       'read'    => function($id){   echo "SHOW bucket($id)"; },
   *       'update'  => function($id){   echo "MODIFY bucket($id)"; },
   *       'delete'  => function($id){   echo "DELETE bucket($id)"; },
   *       'list'    => function(){      echo "All buckets"; },
   *       'clear'   => function(){      echo "Cleared all buckets"; },
   *     ]);
   *   });
   * 
   * </code>
   * 
   * @param  string $element The resource name.
   * @param  array  $maps A map of actions callbacks for different CRUD actions.
   */
  
  public static function expose($element,array $maps=null){
    if(null === $maps && is_array($element)){
      $maps = $element;
      $collection = '';
    } else {
      $collection = '/'.$element;
    }
    return Route::group($collection,function() use ($maps){
      $actions = [];
      if(isset($maps['list']))    $actions['get']    = $maps['list'];
      if(isset($maps['create']))  $actions['post']   = $maps['create'];
      if(isset($maps['clear']))   $actions['delete'] = $maps['clear'];
      Route::map('/',$actions);
  
      $actions = [];
      if(isset($maps['read']))    $actions['get']    = $maps['read'];
      if(isset($maps['update']))  $actions['put']    = $maps['update'];
      if(isset($maps['delete']))  $actions['delete'] = $maps['delete'];
      Route::map("/:id",$actions);
    });
  }

}
