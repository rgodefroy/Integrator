<?php
namespace ThalosGears;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NativeMailerHandler;

class Logger extends MonologLogger
{
  public function loadHandlers(Array $handlers) {
    foreach ($handlers as $handler => $options) {
      $loader = 'load'.ucfirst($handler).'Handler';
      $this->pushHandler($this->$loader($options));
    }
  }
  
  protected function loadFileHandler($options) {
    return new StreamHandler($options['path'], constant('Monolog\Logger::'.$options['level']));
  }
  
  protected function loadMailHandler($options) {
    return new NativeMailerHandler($options['to'], $options['subject'], $options['from'], constant('Monolog\Logger::'.$options['level']));
  }
}
