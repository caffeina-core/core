<?php

/**
 * CLI
 *
 * CLI command routing.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

class CLI {
    use Module, Events;

    protected static $file         = null,
                     $arguments    = [],
                     $options      = [],
                     $commands     = [],
                     $help         = null,
                     $error        = null,
                     $color_stack  = ['NORMAL'],
                     $shell_colors = [
                      'BLACK'     =>"\033[0;30m",      'DARKGRAY'     =>"\033[1;30m",
                      'BLUE'      =>"\033[0;34m",      'LIGHTBLUE'    =>"\033[1;34m",
                      'GREEN'     =>"\033[0;32m",      'LIGHTGREEN'   =>"\033[1;32m",
                      'CYAN'      =>"\033[0;36m",      'LIGHTCYAN'    =>"\033[1;36m",
                      'RED'       =>"\033[0;31m",      'LIGHTRED'     =>"\033[1;31m",
                      'PURPLE'    =>"\033[0;35m",      'LIGHTPURPLE'  =>"\033[1;35m",
                      'BROWN'     =>"\033[0;33m",      'YELLOW'       =>"\033[1;33m",
                      'LIGHTGRAY' =>"\033[0;37m",      'WHITE'        =>"\033[1;37m",
                      'NORMAL'    =>"\033[0;37m",      'B'            =>"\033[1m",
                      'ERROR'     =>"\033[1;31m",      'INFO'         =>"\033[0;36m",
                      'I'         =>"\033[0;30;104m",  'IB'           =>"\033[1;30;104m",
                      'U'         =>"\033[4m",         'D'            =>"\033[2m",
                    ];

    /**
     * Bind a callback to a command route
     * @param  string   $command  The command route, use ":" before a parameter for extraction.
     * @param  callable $callback The callback to be binded to the route.
     */
    public static function on($command,callable $callback,$description=''){
      $parts = preg_split('/\s+/',$command);
      static::$commands[array_shift($parts)] = [$parts,$callback,$description];
    }

    /**
     * Bind a callback to the "help" route.
     * @param  callable $callback The callback to be binded to the route. If omitted triggers the callback.
     */
    public static function help(callable $callback = null){
        $callback
          ? is_callable($callback) && static::$help = $callback
          : static::$help && call_user_func(static::$help);
    }

    /**
     * Bind a callback when an error occurs.
     * @param  callable $callback The callback to be binded to the route. If omitted triggers the callback.
     */
    public static function error(callable $callback = null){
        $callback
          ? is_callable($callback) && static::$error = $callback
          : static::$error && call_user_func(static::$error);
    }

    /**
     * Returns the script name.
     * @return string
     */
    public static function name(){
        return static::$file;
    }

    /**
     * Triggers an error and exit
     * @param string $message
     */
    protected static function triggerError($message){
        is_callable(static::$error) && call_user_func(static::$error,$message);
        exit -1;
    }

    /**
     * Get a passed option
     * @param string $key The name of the option paramenter
     * @param mixed $default The default value if parameter is omitted. (if a callable it will be evaluated)
     * @return mixed
     */
    public static function input($key=null,$default=null){
      return $key ? (isset(static::$options[$key]) ? static::$options[$key] : (is_callable($default)?call_user_func($default):$default)) : static::$options;
    }

    /**
     * Returns an explanation for the supported commands
     *
     * @method commands
     *
     * @return array   The commands and their description.
     */
    public static function commands(){
       $results = [];
       foreach(static::$commands as $name => $cmd){
          $results[] = [
            'name'        => $name,
            'params'      => preg_replace('/:(\w+)/','[$1]',implode(' ',$cmd[0])),
            'description' => $cmd[2],
          ];
       }
       return $results;
    }

    /**
     * Dispatch the router
     * @param  string[] $args The arguments array.
     * @return boolean  True if route was correctly dispatched.
     */
    public static function run($args=null){
      if($args) {
        $_args = $args;
        static::$file = basename(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:__FILE__);
       } else {
        $_args = $_SERVER['argv'];
        static::$file = basename(array_shift($_args));
      }
      foreach($_args as $e) if(strpos($e,'-')===0) {
        $h = explode('=',$e);
        static::$options[ltrim(current($h),'-')] = isset($h[1])?$h[1]:true;
      } else {
        static::$arguments[] = $e;
      }

      if(isset(static::$arguments[0])){
        $command = array_shift(static::$arguments);
        if (empty(static::$commands[$command]))
          return static::triggerError("Unknown command [".$command."].");
        $cmd = static::$commands[$command];
        $pars_vector = [];
        foreach ($cmd[0] as $_idx => $segment) {
          if ($segment[0]==':'){
            // Extract paramenter
            if (isset(static::$arguments[$_idx])){
               $pars_vector[] = static::$arguments[$_idx];
            } else return static::triggerError("Command [".$command."] needs more parameters");
          } else {
            // Match command
            if (empty(static::$arguments[$_idx]) || $segment!=static::$arguments[$_idx])
              return static::triggerError("Command [".$command."] is incomplete.");
          }
        }
        call_user_func_array($cmd[1],$pars_vector);
        return true;
      } else {
        static::help();
        return false;
      }
    }

   /**
    * Prints a message to the console with color formatting.
    * @param  string $message The html-like encoded message
    * @return void
    */
   public static function write($message){
      if( preg_match('~<[^>]+>~',$message)) {
         // Use preg_replace_callback for fast regex matches navigation
         echo strtr(preg_replace_callback('~^(.*)<([^>]+)>(.+)</\2>(.*)$~USm',function($m){
            static::write($m[1]);
            $color = strtoupper(trim($m[2]));
            if( isset(static::$shell_colors[$color]) ) echo static::$shell_colors[$color];
            static::$color_stack[] = $color;
            static::write($m[3]);
            array_pop(static::$color_stack);
            $back_color = array_pop(static::$color_stack) ?: static::$color_stack[]='NORMAL';
            if( isset(static::$shell_colors[$back_color]) ) echo static::$shell_colors[$back_color];
            static::write($m[4]);
         },strtr($message,["\n"=>"&BR;"])),["&BR;"=>PHP_EOL]);
      } else {
         echo strtr($message,["&BR;"=>PHP_EOL]);
      }
   }

   /**
    * Like CLI::write, but appends a newline at the end.
    * @param  string $message The html-like encoded message
    * @return void
    */
   public static function writeln($message){
       static::write($message . PHP_EOL);
   }

   /**
    * Set output ANSI color
    * @param string $color The color name constant.
    * @return void
    */
   public static function color($color){
       if ( isset(static::$shell_colors[$color]) ) echo static::$shell_colors[$color];
   }

   /**
    * Edit a temporary block of text with $EDITOR (or nano as fallback)
    * @param string $text The initial text of the document.
    * @param string $filename The (fake) filename passed to the editor (for syntax highlighting hint).
    * @return string The edited contents
    */
   public static function edit($text, $filename=''){
      $EDITOR = getenv('EDITOR')?:'nano';
      $tmp = tempnam(sys_get_temp_dir(), "E-").strtr($filename,'/','_');
      file_put_contents($tmp, $text);
      passthru("$EDITOR $tmp");
      $result = file_get_contents($tmp);
      unlink($tmp);
      return $result;
  }


}


// Standard Help Message
CLI::help(function(){
  echo 'Usage: ', CLI::name(),' [commands]', PHP_EOL,
       'Commands:',PHP_EOL;
  foreach( CLI::commands() as $cmd ){
    echo "\t", $cmd['name'], ' ' ,$cmd['params'], PHP_EOL;
    if($cmd['description'])
      echo "\t\t- ", str_replace("\n","\n\t\t  ",$cmd['description']), PHP_EOL, PHP_EOL;
  }
});

// Standard Error Message
CLI::error(function($message){
  echo 'Error: ',$message,PHP_EOL;
});
