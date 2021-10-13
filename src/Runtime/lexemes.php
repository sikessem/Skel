<?php

namespace Skel\Runtime;

return [
    keyword('let'),
    keyword('set'),
    keyword('get'),
    keyword('put'),
    keyword('check'),
    keyword('isset'),
    keyword('unset'),
    keyword('reset'),

    special('add', '+'),
    special('sub', '-'),
    special('mul', '*'),
    special('div', '/'),
    special('rem', '%'),
    special('exp', '^'),
    special('colon', ':'),
    special('equal', '='),
    special('comma', ','),
    special('semicolon', ';'),

    pattern('id', '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*'),
    pattern('float', '\d+\.\d*'),
    pattern('digit', '\d+'),
    pattern('blank', '[ \t]+'),
    pattern('newLine', '[\r\n]+'),
    pattern('space', '[\s]+'),
    pattern('unknown', '[^\S]+'),

    context('note', [
      'from' => '<!',
      'to' => '!>',
      'escape' => false,
    ]),
    context('code', [
      'from' => '<?',
      'to' => '?>',
      'escape' => false,
    ]),
    context('data', [
      'from' => '`',
      'to' => '`',
    ]),
    context('byte', [
      'from' => '\'',
      'to' => '\'',
    ]),
    context('text', [
      'from' => '"',
      'to' => '"',
    ]),
];