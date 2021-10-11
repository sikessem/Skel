<?php

namespace Skel\Runtime;

use \Phar;

class Archiver {
    public function __construct(protected Phar $phar, string $dir) {
        $this->setDir($dir);
    }

    protected string $dir;

    public function setDir(string $dir): void {
        $this->dir = realpath($dir) . DIRECTORY_SEPARATOR;
    }

    public function getDir(): string {
        return $this->dir;
    }

    protected function pathOf(string $path): string {
        $realpath = realpath($path);
        $pos = strpos($realpath, $this->dir);
        $path = (false !== $pos) ? substr_replace($realpath, '', $pos, strlen($this->dir)) : $realpath;
        $path = strtr($path, '\\', '/');
        return $path;
    }

    public function archive(): void {
        $this->start($this->dir);
    }

    public function start(string $dir) {
        if ($names = scandir($dir))
            foreach ($names as $name)
                $this->build($dir, $name);
    }

    public function build(string $dir, string $file): void {
        if (!in_array($file, ['.', '..'])) {
            $absolutePath = $dir . $file;
            $relativePath = $this->pathOf($absolutePath);

            if ($this->excluded($relativePath))
                return;
            
            if ($this->included($relativePath)) {
                $this->addFile($absolutePath, $relativePath);
                return;
            }

            if (is_dir($absolutePath)) {
                $this->start($absolutePath . DIRECTORY_SEPARATOR);
                return;
            }

            if (
                $this->matchFile('bin/*', $relativePath) &&
                ($content = file_get_contents($absolutePath)) &&
                $content !== ($replacement = preg_replace('/^\#\!\/usr\/bin\/env php\s*/', '', $content))
            ) {
                $this->addCode($replacement, $relativePath);
                return;
            }
            
            if ($this->matchFile('*.php', $relativePath)) {
                $content = file_get_contents($absolutePath);
                $this->addCode($content, $relativePath);
            }
        }
    }

    public function addFile(string $absolutePath, string $relativePath): void {
        echo "Add file $absolutePath as $relativePath" . PHP_EOL;
        $this->phar->addFile($absolutePath, $relativePath);
    }

    protected function addCode(string $content, string $path, bool $compiled = true): void {
        echo "Add code $path" . PHP_EOL;
        $this->phar->addFromString($path, $compiled ? Compiler::compileCode($content) : $content);
    }

    protected array $exclude = [];
    protected array $include = [];

    protected function exclude(string $type, string $pattern): static {
        if (is_int($key = array_search($pattern, $this->include[$type] ??= [], true)))
            unset($this->include[$type][$key]);
        $this->exclude[$type][] = $pattern;
        return $this;
    }

    public function include(string $type, string $pattern): static {
        if (is_int($key = array_search($pattern, $this->exclude[$type] ??= [], true)))
            unset($this->exclude[$type][$key]);
        $this->include[$type][] = $pattern;
        return $this;
    }

    public function excluded(string $name): bool {
        foreach ($this->exclude as $excludedType => $excludedPatterns)
            foreach ($excludedPatterns as $excludedPattern)
                if ($this->{'match' . ucfirst($excludedType)}($excludedPattern, $name))
                    return true;
        return false;
    }

    public function included(string $name): bool {
        foreach ($this->include as $includedType => $includedPatterns)
            foreach ($includedPatterns as $includedPattern)
                if ($this->{'match' . ucfirst($includedType)}($includedPattern, $name))
                    return true;
        return false;
    }

    public function excludeFiles(string|array ...$patterns): static {
        foreach ($patterns as $pattern)
            is_array($pattern) ? $this->excludeFiles(...$pattern) : $this->excludeFile($pattern);
        return $this;
    }

    public function includeFiles(string|array ...$patterns): static {
        foreach ($patterns as $pattern)
            is_array($pattern) ? $this->includeFiles(...$pattern) : $this->includeFile($pattern);
        return $this;
    }

    public function excludeFile(string $pattern): static {
        return $this->exclude('file', $pattern);
    }

    public function includeFile(string $pattern): static {
        return $this->include('file', $pattern);
    }

    public function matchFile(string $pattern, string $name, int $flags = 0): bool {
        return (bool) fnmatch($pattern, $name, $flags);
    }

    public function includePaths(string|array ...$patterns): static {
        foreach ($patterns as $pattern)
            is_array($pattern) ? $this->includePaths(...$pattern) : $this->includePath($pattern);
        return $this;
    }

    public function excludePaths(string|array ...$patterns): static {
        foreach ($patterns as $pattern)
            is_array($pattern) ? $this->excludePaths(...$pattern) : $this->excludePath($pattern);
        return $this;
    }

    public function excludePath(string $pattern): static {
        return $this->exclude('path', $pattern);
    }

    public function includePath(string $pattern): static {
        return $this->include('path', $pattern);
    }

    public function matchPath(string $pattern, string $name, int $flags = 0): bool {
        return (bool) preg_match($pattern, $name, flags: $flags);
    }
}