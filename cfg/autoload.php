<?php
/*
 +------------------------------------------+
 |         THE SKEL OBJECT AUTOLOAD         |
 +------------------------------------------+
 | Author : SIGUI Kessé Emmanuel (SIKessEm) |
 | License : Apache 2.0                     |
 +------------------------------------------+
 */
(function(){
  spl_autoload_register(function(string $object) {
    if(preg_match('/^'. preg_quote(LIB_NS, '/') .'(.*)$/', $object, $matches)) {
      require_once LIB_DIR . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $matches[1]) . LIB_EXT;
    }
  }, true, true);
})();
