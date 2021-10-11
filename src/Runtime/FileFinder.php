<?php

namespace Skel\Runtime;

class FileFinder extends Finder {
    public static function match(string $pattern, string $name, int $flags = 0): bool {
        return (bool) fnmatch($pattern, $name, $flags);
    }
}