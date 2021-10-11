<?php

namespace Skel\Runtime;

abstract class Finder {
    protected array $exclude = [];
    protected array $include = [];

    public function exclude(string|array ...$patterns): static {
        foreach ($patterns as $pattern) {
            if (is_array($pattern))
                $this->exclude(...$pattern);
            else {
                if (is_int($key = array_search($pattern, $this->include, true)))
                    unset($this->include[$key]);
                $this->exclude[] = $pattern;
            }
        }
        return $this;
    }

    public function include(string|array ...$patterns): static {
        foreach ($patterns as $pattern) {
            if (is_array($pattern))
                $this->include(...$pattern);
            else {
                if (is_int($key = array_search($pattern, $this->exclude, true)))
                    unset($this->exclude[$key]);
                $this->include[] = $pattern;
            }
        }
        return $this;
    }

    public function excluded(string $name): bool {
        foreach ($this->exclude as $excluded)
            if ($this->match($excluded, $name))
                return true;
        return false;
    }

    public function included(string $name): bool {
        foreach ($this->include as $included)
            if ($this->match($included, $name))
                return true;
        return false;
    }

    abstract static function match(string $pattern, string $name, int $flags = 0): bool;
}