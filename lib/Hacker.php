<?php namespace Skel;

class Hacker {

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
}
