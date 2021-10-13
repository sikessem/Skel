<?php

namespace Skel\Runtime;

function lexeme(string $type, string $key, mixed $value): Lexeme {
    return new Lexeme($type, $key, $value);
}

function keyword(mixed $value): Lexeme {
    return lexeme('keyword', $value, $value);
}

function special(string $key, mixed $value): Lexeme {
    return lexeme('special', $key, $value);
}

function pattern(string $key, mixed $value): Lexeme {
    return lexeme('pattern', $key, $value);
}

function context(string $key, $value): Lexeme {
    return lexeme('context', $key, $value);
}