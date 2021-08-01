<?php
/*
 +------------------------------------------+
 |  Skel Programming Language Starter File  |
 +------------------------------------------+
 | Author : SIGUI Kessé Emmanuel (SIKessEm) |
 | License : Apache 2.0                     |
 +------------------------------------------+
 */
(function(){
  spl_autoload_register(function(string $object) {
    if(preg_match('/^'. preg_quote('Skel\\', '/') .'(.*)$/', $object, $matches)) {
      require_once  dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $matches[0]) . '.php';
    }
  }, true, true);
})();
