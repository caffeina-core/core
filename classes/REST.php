<?php

/**
 * REST
 *
 * REST utility and router shortcuts.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
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
      $collection_routes = [];
      if(isset($maps['list']))    $collection_routes['get']    = $maps['list'];
      if(isset($maps['create']))  $collection_routes['post']   = $maps['create'];
      if(isset($maps['clear']))   $collection_routes['delete'] = $maps['clear'];
      Route::map('/',$collection_routes);

      $entity_routes = [];
      if(isset($maps['read']))    $entity_routes['get']    = $maps['read'];
      if(isset($maps['update']))  $entity_routes['put']    = $maps['update'];
      if(isset($maps['delete']))  $entity_routes['delete'] = $maps['delete'];
      Route::map('/:id',$entity_routes);

    } else {

      Route::group("/$element",function() use (&$maps){

        $collection_routes = [];
        if(isset($maps['list']))    $collection_routes['get']    = $maps['list'];
        if(isset($maps['create']))  $collection_routes['post']   = $maps['create'];
        if(isset($maps['clear']))   $collection_routes['delete'] = $maps['clear'];
        Route::map('/',$collection_routes);

        $entity_routes = [];
        if(isset($maps['read']))    $entity_routes['get']    = $maps['read'];
        if(isset($maps['update']))  $entity_routes['put']    = $maps['update'];
        if(isset($maps['delete']))  $entity_routes['delete'] = $maps['delete'];
        Route::map('/:id',$entity_routes);

      });
    }
  }

}
