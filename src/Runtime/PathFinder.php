<?php

namespace Skel\Runtime;

class PathFinder extends Finder {
    public static function match(string $pattern, string $name, int $flags = 0): bool {
        return (bool) preg_match($pattern, $name, flags: $flags);
    }
}