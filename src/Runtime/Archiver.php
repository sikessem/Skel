<?php

namespace Skel\Runtime;

use \Phar;

class Archiver {
    public function __construct(protected Phar $phar, string $dir, array $finders = []) {
        $this->setDir($dir);
        $this->addFinders($finders);
    }

    public function addFinders(array $finders): static {
        foreach ($finders as $finder)
            $this->addFinder($finder);
        return $this;
    }

    public function addFinder(Finder $finder): static {
        $this->finders[] = $finder;
        return $this;
    }

    protected array $finders = [];

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

            foreach ($this->finders as $finder)
                if ($finder->excluded($relativePath))
                    return;

            foreach ($this->finders as $finder) {
                if ($finder->included($relativePath)) {
                    $this->addFile($absolutePath, $relativePath);
                    return;
                }
            }

            if (is_dir($absolutePath)) {
                $this->start($absolutePath . DIRECTORY_SEPARATOR);
                return;
            }

            if (
                FileFinder::match('bin/*', $relativePath) &&
                ($content = file_get_contents($absolutePath)) &&
                $content !== ($replacement = preg_replace('/^\#\!\/usr\/bin\/env php\s*/', '', $content))
            ) {
                $this->addCode($replacement, $relativePath);
                return;
            }
            
            if (FileFinder::match('*.php', $relativePath)) {
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
}