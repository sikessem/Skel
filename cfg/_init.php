<?php

define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);

define('SRC_DIR', ROOT_DIR . 'src' . DIRECTORY_SEPARATOR);
define('SRC_EXT', '.skel');

define('OUT_DIR', ROOT_DIR . 'out' . DIRECTORY_SEPARATOR);
define('OUT_EXT', '.php');

const KEYWORDS = [
  'let' => 'let',
  'when' => 'when',
  'then' => 'then',
  'else' => 'else',
  'for' => 'for',
  'do' => 'do',
  'is' => 'is',
  'in' => 'in',
  'of' => 'of',
  'to' => 'to',
  'ok' => 'ok',
  'on' => 'on',
  'yes' => 'yes',
  'off' => 'off',
  'no' => 'no',
  'not' => 'not',
  'use' => 'use',
  'as' => 'as',
  'equal' => '=',
  'add' => '+',
  'sub' => '-',
  'mul' => '*',
  'div' => '/',
  'rem' => '%',
  'colon' => ':',
  'semicolon' => ';',
];

const PATTERNS = [
  'num' => '\d+',
  'var' => '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*',
];
