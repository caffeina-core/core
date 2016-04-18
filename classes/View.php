<?php

/**
 * View
 *
 * View template handling class.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class View {
    use Module;

    protected static $handler = null;
    protected $options = [
      'template'  => '',
      'data'      => [],
    ];

    /**
     * Construct a new view based on the passed template
     * @param  string $template The template path
     */
    public function __construct($template){
      $this->options['template'] = $template;
    }

    /**
     * Load a Template Handler
     * @param  class $handler The template handler class instance
     */
    public static function using(View\Adapter &$handler){
      static::$handler = $handler;
    }

    /**
     * View factory method, can optionally pass data to pre-init view
     * @param  string $template The template path
     * @param  array $data     The key-value map of data to pass to the view
     * @return View
     */
    public static function from($template,$data=null){
      $view = new self($template);
      return $data ? $view->with($data) : $view;
    }

    /**
     * Assigns data to the view
     * @param  array $data     The key-value map of data to pass to the view
     * @return View
     */
    public function with($data){
      if ($data){
        $tmp = array_merge($data, (isset($this->options['data'])?$this->options['data']:[]));
        $this->options['data'] = $tmp;
      }
      return $this;
    }

    /**
     * Render view when casted to a string
     * @return string The rendered view
     */
    public function __toString(){
      return Filter::with('core.view',static::$handler->render($this->options['template'],$this->options['data']));
    }

    /**
     * Returns the handler instance
     * @return mixed
     */
    public static function & handler(){
      return static::$handler;
    }

    /**
     * Check if a template exists
     * @return bool
     */
    public static function exists($templatePath){
      return static::$handler->exists($templatePath);
    }


    /**
     * Propagate the call to the handler
     */
    public function __call($n,$p){
      return call_user_func_array([static::$handler,$n],$p);
    }

    /**
     * Propagate the static call to the handler
     */
    public static function __callStatic($n,$p){
      return forward_static_call_array([static::$handler,$n],$p);
    }

}
