<?php

/**
 * CLI/Command
 *
 * A routable CLI command.
 *
 * @package core
 *
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015-2017 - http://caffeina.com
 */

namespace Core\CLI;

class Command {
  public $name,
  $options,
  $arguments,
  $title,
  $description,
  $help,
    $callback;

  public function __construct($name) {
    $this->name = $name;
  }

  public function option($name, $description = '', $options = []) {
    $name                 = trim($name);
    $required             = '*' == $name[-1];
    $name                 = trim($name, '*');
    $this->options[$name] = (object) [
      'name'        => $name,
      'description' => $description,
      'required'    => $required,
      'default'     => $options['default'] ?? false,
    ];
    return $this;
  }

  public function argument($name, $description = '', $options = []) {
    $name                   = trim($name);
    $required               = '?' != $name[-1];
    $name                   = trim($name, '?');
    $this->arguments[$name] = (object) [
      'name'        => $name,
      'description' => $description,
      'required'    => $required,
      'default'     => $options['default'] ?? null,
    ];
    return $this;
  }

  public function title($value) {
    $this->title = $value;
    return $this;
  }

  public function description($value) {
    $this->description = $value;
    return $this;
  }

  public function help($value) {
    $this->help = $value;
    return $this;
  }

  public function run($callback) {
    $this->callback = $callback;
    return $this;
  }

  function switch ($arg, $map) {
      $this->callback = function () use ($arg, $map) {
        $key = \Core\CLI::get($arg) ?? '*';
        if (isset($map[$key])) {
          call_user_func($map[$key]);
        }

      };
      return $this;
  }

  public function exec($inputs) {
    $data = [];
    $idx  = 0;

    // Arguments
    foreach ($this->arguments as $name => $e) {
      if ($e->required) {
        if (isset($inputs->args[$idx])) {
          $data[$e->name] = $inputs->args[$idx];
        } else {
          return (bool) \Core\CLI::trigger('error', "Argument $e->name is mandatory.");
        }

      } else {
        $data[$e->name] = $e->default;
      }
      $idx++;
    }

    // Options
    foreach ($this->options as $name => $e) {
      if ($e->required) {
        if (isset($inputs->opts->$name)) {
          $data[$e->name] = $inputs->opts->$name;
        } else {
          return (bool) \Core\CLI::trigger('error', "Option $e->name is mandatory.");
        }

      } else {
        $data[$e->name] = $e->default;
      }
    }

    call_user_func($this->callback, (object) $data);

    return true;
  }

}