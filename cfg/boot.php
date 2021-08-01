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
    if(preg_match('/^'. preg_quote('Skel\\', '/') .'(.*)$/', $object, $matches))
      foreach(['lib', 'try'] as $dir)
        if(is_file($file = dirname(__DIR__) . str_replace('\\', DIRECTORY_SEPARATOR, "\\$dir\\$matches[0]") . '.php')) {
          require_once $file;
          return true;
        }
    return false;
  }, true, true);
})();
