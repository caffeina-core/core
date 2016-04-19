<?php

/**
 * Route
 *
 * URL Router and action dispatcher.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Route {
    use Module;

    protected static $routes,
                     $base    = '',
                     $prefix  = [],
                     $group   = [];

    protected $URLPattern     = '',
              $pattern        = '',
              $dynamic        = false,
              $callback       = null,
              $methods        = [],
              $befores        = [],
              $afters         = [],

              $rules          = [],
              $response       = '';


    /**
     * Create a new route definition. This method permits a fluid interface.
     *
     * @param string $URLPattern The URL pattern, can be used named parameters for variables extraction
     * @param $callback The callback to invoke on route match.
     * @param string $method The HTTP method for which the route must respond.
     * @return Route
     */
    public function __construct($URLPattern, $callback = null, $method='get'){
        $this->URLPattern = rtrim(implode('',static::$prefix),'/') . '/' . trim($URLPattern,'/')?:'/';
        $this->URLPattern = $this->URLPattern != '/' ? rtrim($this->URLPattern,'/') : $this->URLPattern;
        $this->dynamic    = $this->isDynamic($this->URLPattern);
        $this->pattern    = $this->dynamic ? $this->compilePatternAsRegex($this->URLPattern, $this->rules) : $this->URLPattern;
        $this->callback   = $callback;

        // We will use hash-checks, for O(1) complexity vs O(n)
        $this->methods[$method] = 1;
        return static::add($this);
    }

    /**
     * Check if route match on a specified URL and HTTP Method.
     * @param  [type] $URL The URL to check against.
     * @param  string $method The HTTP Method to check against.
     * @return boolean
     */
    public function match($URL,$method='get'){
        $method = strtolower($method);

        // * is an http method wildcard
        if (empty($this->methods[$method]) && empty($this->methods['*'])) return false;
        $URL = rtrim($URL,'/');
        $args = [];
        if ( $this->dynamic
               ? preg_match($this->pattern,$URL,$args)
               : $URL == rtrim($this->pattern,'/')
        ){
            foreach ($args as $key => $value) {
              if ( false === is_string($key) ) unset($args[$key]);
            }
            return $args;
        }
        return false;
    }

    /**
     * Run one of the mapped callbacks to a passed HTTP Method.
     * @param  array  $args The arguments to be passed to the callback
     * @param  string $method The HTTP Method requested.
     * @return array The callback response.
     */
    public function run(array $args,$method='get'){
        $method = strtolower($method);
        $this->response 			 		= '';
        $this->response_object 		= null;
       	$this->response_is_object = false;

        // Call direct befores
        if ( $this->befores ) {
          // Reverse befores order
          foreach (array_reverse($this->befores) as $mw) {
	        	ob_start();
            $mw_result = call_user_func($mw->bindTo($this));
          	$this->response .= ob_get_clean();
            if ( false  === $mw_result ) {
            	return [''];
            } else if (is_a($mw_result,'View') || is_string($mw_result)) {
              $this->response .= (string)$mw_result;
          	}
          }
        }

        Event::trigger('core.route.before', $this);

        $callback = (is_array($this->callback) && isset($this->callback[$method]))
                    ? $this->callback[$method] : $this->callback;

        if (is_callable($callback)) {
          // Capure callback output
					Response::type(Response::TYPE_HTML);
	        ob_start();
	        // Silence "Cannot bind an instance to a static closure" warnings
	        $view_results 	 = call_user_func_array(@$callback->bindTo($this), $args);
	        $this->response .= ob_get_clean();

	        // Render View if returned, else echo string or encode json response
	        if ( null !== $view_results ) {
	          if (is_a($view_results,'View') || is_string($view_results)) {
	              $this->response .= (string)$view_results;
	          } else {
			        	$this->response_is_object = true;
	              $this->response_object 		= $view_results;
	          }
	        }

        } else if (is_a($callback,'View') || is_string($callback)) {
          // return rendered View or direct string
        	$this->response .= (string)$callback;
        } else {
          // JSON encode returned value
        	$this->response_is_object = true;
        	$this->response_object 		= $callback;
        }

        // Apply afters
        if ( $this->afters ) {
          foreach ($this->afters as $mw) {
	        	ob_start();
            $mw_result = call_user_func($mw->bindTo($this));
          	$this->response .= ob_get_clean();
            if ( false  === $mw_result ) {
            	return [''];
            } else if (is_a($mw_result,'View') || is_string($mw_result)) {
              $this->response .= (string)$mw_result;
          	}
          }
        }

        Event::trigger('core.route.after', $this);

        if ( $this->response_is_object ){
					$this->response = Response::json($this->response_object);
        } else {
					Response::add($this->response);
        }

        Event::trigger('core.route.end', $this);

        return [Filter::with('core.route.response', $this->response)];
    }

    /**
     * Check if route match URL and HTTP Method and run if it is valid.
     * @param  [type] $URL The URL to check against.
     * @param  string $method The HTTP Method to check against.
     * @return array The callback response.
     */
    public function runIfMatch($URL, $method='get'){
        return ($args = $this->match($URL,$method)) ? $this->run($args,$method) : null;
    }

    /**
     * Start a route definition, default to HTTP GET.
     * @param  string $URLPattern The URL to match against, you can define named segments to be extracted and passed to the callback.
     * @param  $callback The callback to be invoked (with variables extracted from the route if present) when the route match the request URI.
     * @return Route
     */
    public static function on($URLPattern, $callback = null){
        return new Route($URLPattern,$callback);
    }

    /**
     * Start a route definition, for any HTTP Method (using * wildcard).
     * @param  string $URLPattern The URL to match against, you can define named segments to be extracted and passed to the callback.
     * @param  $callback The callback to be invoked (with variables extracted from the route if present) when the route match the request URI.
     * @return Route
     */
    public static function any($URLPattern, $callback = null){
        return (new Route($URLPattern,$callback))->via('*');
    }

    /**
     * Bind a callback to the route definition
     * @param  $callback The callback to be invoked (with variables extracted from the route if present) when the route match the request URI.
     * @return Route
     */
    public function & with($callback){
        $this->callback = $callback;
        return $this;
    }

    /**
     * Bind a middleware callback to invoked before the route definition
     * @param  callable $before The callback to be invoked ($this is binded to the route object).
     * @return Route
     */
    public function & before($callback){
        $this->befores[] = $callback;
        return $this;
    }

    /**
     * Bind a middleware callback to invoked after the route definition
     * @param  $callback The callback to be invoked ($this is binded to the route object).
     * @return Route
     */
    public function & after($callback){
        $this->afters[] = $callback;
        return $this;
    }

    /**
     * Defines the HTTP Methods to bind the route onto.
     *
     * Example:
     * <code>
     *  Route::on('/test')->via('get','post','delete');
     * </code>
     *
     * @return Route
     */
    public function & via(){
        $this->methods = [];
        foreach (func_get_args() as $method){
            $this->methods[strtolower($method)] = true;
        }
        return $this;
    }

    /**
     * Defines the regex rules for the named parameter in the current URL pattern
     *
     * Example:
     * <code>
     *  Route::on('/proxy/:number/:url')
     *    ->rules([
     *      'number'  => '\d+',
     *      'url'     => '.+',
     *    ]);
     * </code>
     *
     * @param  array  $rules The regex rules
     * @return Route
     */
    public function & rules(array $rules){
        foreach ((array)$rules as $varname => $rule){
            $this->rules[$varname] = $rule;
        }
        $this->pattern = $this->compilePatternAsRegex($this->URLPattern,$this->rules);
        return $this;
    }

    /**
     * Map a HTTP Method => callable array to a route.
     *
     * Example:
     * <code>
     *  Route::map('/test'[
     *      'get'     => function(){ echo "HTTP GET"; },
     *      'post'    => function(){ echo "HTTP POST"; },
     *      'put'     => function(){ echo "HTTP PUT"; },
     *      'delete'  => function(){ echo "HTTP DELETE"; },
     *    ]);
     * </code>
     *
     * @param  string $URLPattern The URL to match against, you can define named segments to be extracted and passed to the callback.
     * @param  array $callbacks The HTTP Method => callable map.
     * @return Route
     */
    public static function & map($URLPattern, $callbacks = []){
        $route           = new static($URLPattern);
        $route->callback = [];
        foreach ($callbacks as $method => $callback) {
           $method = strtolower($method);
           if (Request::method() !== $method) continue;
           $route->callback[$method] = $callback;
           $route->methods[$method]  = 1;
        }
        return $route;
    }

    /**
     * Compile an URL schema to a PREG regular expression.
     * @param  string $pattern The URL schema.
     * @return string The compiled PREG RegEx.
     */
    protected static function compilePatternAsRegex($pattern, $rules=[]){
        return '#^'.preg_replace_callback('#:([a-zA-Z]\w*)#S',function($g) use (&$rules){
            return '(?<' . $g[1] . '>' . (isset($rules[$g[1]])?$rules[$g[1]]:'[^/]+') .')';
        },str_replace(['.',')','*'],['\.',')?','.+'],$pattern)).'$#';
    }

    /**
     * Extract the URL schema variables from the passed URL.
     * @param  string  $pattern The URL schema with the named parameters
     * @param  string  $URL The URL to process, if omitted the current request URI will be used.
     * @param  boolean $cut If true don't limit the matching to the whole URL (used for group pattern extraction)
     * @return array The extracted variables
     */
    protected static function extractVariablesFromURL($pattern, $URL=null, $cut=false){
        $URL     = $URL ?: Request::URI();
        $pattern = $cut ? str_replace('$#','',$pattern).'#' : $pattern;
        if ( !preg_match($pattern,$URL,$args) ) return false;
        foreach ($args as $key => $value) {
            if (false === is_string($key)) unset($args[$key]);
        }
        return $args;
    }

    /**
     * Check if an URL schema need dynamic matching (regex).
     * @param  string  $pattern The URL schema.
     * @return boolean
     */
    protected static function isDynamic($pattern){
      return strlen($pattern) != strcspn($pattern,':(?[*+');
    }

    /**
     * Add a route to the internal route repository.
     * @param Route $r
     * @return Route
     */
    public static function add($r){
        if ( isset(static::$group[0]) ) static::$group[0]->add($r);
        return static::$routes[implode('',static::$prefix)][] = $r;
    }

    /**
     * Define a route group, if not immediatly matched internal code will not be invoked.
     * @param  string $prefix The url prefix for the internal route definitions.
     * @param  string $callback This callback is invoked on $prefix match of the current request URI.
     */
    public static function group($prefix,$callback=null){

        // Skip definition if current request doesn't match group.

        $prefix_complete = rtrim(implode('',static::$prefix),'/') . $prefix;

        if ( static::isDynamic($prefix) ){

            // Dynamic group, capture vars
            $vars = static::extractVariablesFromURL(static::compilePatternAsRegex($prefix_complete),null,true);

            // Errors in compile pattern or variable extraction, aborting.
            if (false === $vars) return;

            static::$prefix[] = $prefix;
            if (empty(static::$group)) static::$group = [];
            array_unshift(static::$group, new RouteGroup());
            if ($callback) call_user_func_array($callback,$vars);
            $group = static::$group[0];
            array_shift(static::$group);
            array_pop(static::$prefix);
            if (empty(static::$prefix)) static::$prefix=[''];
            return $group;

        } else if ( 0 === strpos(Request::URI(), $prefix_complete) ){

            // Static group
            static::$prefix[] = $prefix;
            if (empty(static::$group)) static::$group = [];
            array_unshift(static::$group, new RouteGroup());
            if ($callback) call_user_func($callback);
            $group = static::$group[0];
            array_shift(static::$group);
            array_pop(static::$prefix);
            if (empty(static::$prefix)) static::$prefix=[''];
            return $group;
        } else {

            // Null Object
            return new RouteGroup();
        }

    }

    public static function exitWithError($code,$message="Application Error"){
    	Response::error($code,$message);
    	Response::send();
    	exit;
    }

    /**
     * Start the route dispatcher and resolve the URL request.
     * @param  string $URL The URL to match onto.
     * @return boolean true if a route callback was executed.
     */
    public static function dispatch($URL=null,$method=null){
        if (!$URL)     $URL     = Request::URI();
        if (!$method)  $method  = Request::method();

        if (Options::get('core.response.autosend',true)){
          $__deferred_send = new Deferred('Response::send');
        }

        foreach ((array)static::$routes as $group => $routes){
            foreach ($routes as $route) {
                if (is_a($route, 'Route') && false !== ($args = $route->match($URL,$method))){
                    $route->run($args,$method);
                    return true;
                }
            }
        }

        Response::status(404, '404 Resource not found.');
        Event::trigger(404);
        return false;
    }
}

class RouteGroup {
  protected $routes;

  public function __construct(){
    $this->routes = new SplObjectStorage;
    return Route::add($this);
  }

  public function has($r){
    return $this->routes->contains($r);
  }

  public function add($r){
    $this->routes->attach($r);
    return $this;
  }

  public function remove($r){
    if ($this->routes->contains($r)) $this->routes->detach($r);
    return $this;
  }

  public function before($callbacks){
    foreach ($this->routes as $route){
      $route->before($callbacks);
    }
    return $this;
  }

  public function after($callbacks){
    foreach ($this->routes as $route){
      $route->after($callbacks);
    }
    return $this;
  }

}

