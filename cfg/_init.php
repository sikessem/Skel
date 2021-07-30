<?php
define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);

define('SRC_DIR', ROOT_DIR . 'src' . DIRECTORY_SEPARATOR);
define('OUT_DIR', ROOT_DIR . 'out' . DIRECTORY_SEPARATOR);
define('CFG_DIR', ROOT_DIR . 'cfg' . DIRECTORY_SEPARATOR);
define('LIB_DIR', ROOT_DIR . 'lib' . DIRECTORY_SEPARATOR);

define('SRC_EXT', '.skel');
define('OUT_EXT', '.php');
define('LIB_EXT', '.php');

define('LIB_NS', 'Skel\\');

define('KEYWORDS', require CFG_DIR . 'keywords.php');
define('PATTERNS', require CFG_DIR . 'patterns.php');

require_once CFG_DIR . 'autoload.php';
