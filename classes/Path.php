<?php

/**
 * Path
 *
 * URL Router.
 * 
 * @package core
 * @author stefano.azzolini@caffeinalab.com 
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class Path {    
    
    protected static $routes        = [],
                     $prefixes      = [],
                     $routes_rx     = '';

    protected        $name          = '',
                     $path          = '',
                     $methods       = ['*'=>null],
                     $rules         = [],
                     $before        = null,
                     $after         = null;
    
    public static function on($path, callable $callback = null){
        return (new static($path))->with($callback,'get');
    }

    public static function any($path, callable $callback = null){
        return (new static($path))->with($callback);
    }
    
    public static function group($path, callable $body){
        return new PathGroup($path, $body);
    }

    public static function map($path, array $handlers){
        return (new static($path))->methods($handlers);
    }

    public function methods(array $handlers){
        foreach ($handlers as $method => $callback) $this->with($callback, $method);
        return $this;
    }
    
    public function __construct($path) {
        static $idx = 0;
        $this->path     = '/' . trim(implode('/',static::$prefixes) . $path, '/');
        $this->name     = $idx++;
        static::$routes[$this->name] = $this;
        Event::trigger('core.route.add',$this);
    }

    public function rules(array $rules) {
        $this->rules = array_merge($rules,$this->rules);
        return $this;
    }


    public function with(callable $callback, $method = '*') {
        if (is_callable($callback)) $this->methods[strtolower($method)] = $callback;
        return $this;
    }

    // Legacy for old Route compatibility
    public function via() {
        $mh = isset($this->methods['get']) ? $this->methods['get'] : 
              (isset($this->methods['*']) ? $this->methods['*'] : function(){});
        foreach(func_get_args() as $method){
            $this->methods[strtolower($method)] = $mh;
        }
        return $this;
    }


    public function before(callable $callback, $priority = 0) {
        if (is_callable($callback)) {
            if (!$this->before) $this->before = new SplPriorityQueue();
            $this->before->insert($callback,min(100,max(0,$priority)));
        }
        return $this;
    }

    public function after(callable $callback, $priority = 0) {
        if (is_callable($callback)) {
            if (!$this->after) $this->after = new SplPriorityQueue();
            $this->after->insert($callback,min(100,max(0,$priority)));
        }
        return $this;
    }

    public function run($method, array $params = []) {
        $results = null;
        if ($this->canHandleMethod($method = strtolower($method))) {

            // Before
            if ($this->before) {
                foreach($this->before as $cb) call_user_func($cb);
            }

            Event::trigger('core.route.before',$this);
            
            // Path body           
            // Get Method Handler
            $mh = isset($this->methods[$method]) ? $this->methods[$method] : $this->methods['*'];
            ob_start();
            $returned  = call_user_func_array($mh, $params);
            $captured  = ob_get_contents();
            ob_end_clean();

            // Render View if returned, else echo string or encode json response
            if(null !== $returned) {
              if (is_a($returned,'View')) {
                  Response::add($returned->render());
              } else {
                  Response::json($results);
              }
            } else {
                Response::add($captured);
            }

            Event::trigger('core.route.after',$this);
            
            // Afters
            if ($this->after) {
                foreach($this->after as $cb) call_user_func_array($cb,[&$results]);
            }

       }
       return $results;
    }


    protected function compile() {
        $rules = $this->rules;
        $rx = preg_replace_callback('/:\w+/',function($m) use ($rules){
            $g = substr($m[0],1);
            return '('.(isset($rules[$g]) ? $rules[$g] : '[^/]+').')';
        },str_replace(['.','(',')'],['\\.','(?:',')?'], $this->path));
        return $rx;
    }

    protected function canHandleMethod($method) {
        if ($method == '*') return true;
        $method = strtolower($method);       
        return (isset($this->methods[$method]) && is_callable($this->methods[$method])) 
                || is_callable($this->methods['*']);
    }    

    public static function openGroup($prefix) {
        static::$prefixes[] = trim($prefix,'/');
    }

    public static function closeGroup() {
        if (count(static::$prefixes)) return array_pop(static::$prefixes);
    }

    protected static function compilePathsRX($method='*') {
        static::$routes_rx = [];
        foreach (static::$routes as $route) {
            if ($route->canHandleMethod($method)) {
                static::$routes_rx[$route->name] = '(?<R'.$route->name.'>'.$route->compile().')';
            }            
        }
        arsort(static::$routes_rx);
        return static::$routes_rx = '~^(?:' . implode('|',static::$routes_rx) . ')$~AS';
    }
    
    public static function dispatch($url, $method) {
        $rx = static::compilePathsRX($method);
        if(preg_match($rx,rtrim($url,'/')?:'/',$matches)){
            $matches = array_filter(array_slice($matches,1));
            $route = static::$routes[substr(current(array_keys($matches)),1)];
            $route->run($method, array_slice($matches,2));
            return $route;
        } else {
            Response::status(404,'404 Resource not found.');
            Event::trigger(404);
            return false;
        }
    }
    
}

class PathGroup {
    protected $path     = '',
              $routes   = [];

    public function __construct($path,callable $body) {  
        Path::openGroup($path);
        Event::on('core.route.add',[$this,'add']);
        ob_start();
        $body();
        ob_end_clean();
        Event::off('core.route.add',[$this,'add']);
        Path::closeGroup();
    }

    public function add(Path $route) {
        $this->routes[] = $route;
        return $this;
    }

    public function before(callable $callback, $priority = 0) {
        foreach ($this->routes as $r) $r->before($callback, $priority);
        return $this;
    }

    public function after(callable $callback, $priority = 0) {
        foreach ($this->routes as $r) $r->after($callback, $priority);
        return $this;
    }


}
