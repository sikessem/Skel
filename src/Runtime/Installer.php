<?php

namespace Skel\Runtime;

use \Phar;

class Installer {
    public function __construct(string $dir, protected ?string $phar = null) {
        $this->setDir($dir);
    }

    protected string $dir;

    public function setDir(string $dir): void {
        $this->dir = realpath($dir) . DIRECTORY_SEPARATOR;
    }

    public function getDir(): string {
        return $this->dir;
    }

    public function setup(string $file, string $stub, ?int $algo = null): void {
        $phar = new Phar($file, 0, $this->phar);

        if (isset($algo))
            $phar->setSignatureAlgorithm($algo);

        $phar->startBuffering();
        $this->start($phar, $this->dir);
        $phar->setStub(Compiler::compileCode($stub));
        $phar->stopBuffering();
    }

    protected function pathOf(string $path): string {
        $realpath = realpath($path);
        $pos = strpos($realpath, $this->dir);
        $path = (false !== $pos) ? substr_replace($realpath, '', $pos, strlen($this->dir)) : $realpath;
        $path = strtr($path, '\\', '/');
        return $path;
    }

    protected function ignoreFile(string $file): bool {
        foreach ([
            '.gitignore',
            'LICENSE',
            'README.md',
            'composer.*',
            'main.php',
            'skel.phar',
        ] as $ignore)
            if (fnmatch($ignore, $file, FNM_PATHNAME))
                return true;
        return false;
    }

    protected function ignorePath(string $path): bool {
        foreach ([
            '^pkg\/',
            '^.git\/',
        ] as $ignore)
            if (preg_match("/$ignore/", $path))
                return true;
        return false;
    }

    protected function getBin(string $relativePath, string $absolutePath): ?string {
        return
            fnmatch('bin/*', $relativePath) &&
            ($content = file_get_contents($absolutePath)) &&
            $content !== ($replacement = preg_replace('/^\#\!\/usr\/bin\/env php\s*/', '', $content)) ?
                $replacement:
                null;
    }

    protected function getSrc(string $relativePath, string $absolutePath): ?string {
        return fnmatch('*.php', $relativePath) ? file_get_contents($absolutePath) : null;
    }

    protected function addCode(Phar $phar, string $path, string $content, bool $strip = true): void {
        echo "Add code $path" . PHP_EOL;
        $phar->addFromString($path, $strip ? Compiler::compileCode($content) : $content);
    }

    protected function addFile(Phar $phar, string $relativePath, string $absolutePath): void {
        echo "Add file $absolutePath as $relativePath" . PHP_EOL;
        $phar->addFile($absolutePath, $relativePath);
    }

    protected function start(Phar $phar, string $dir): void {
        if ($names = scandir($dir))
            foreach ($names as $name)
                $this->build($phar, $dir, $name);
    }

    protected function build(Phar $phar, string $dir, string $file): void {
        if (!in_array($file, ['.', '..'])) {
            $absolutePath = $dir . $file;
            $relativePath = $this->pathOf($absolutePath);

            if (!$this->ignoreFile($relativePath) && !$this->ignorePath($relativePath)) {
                if (is_dir($absolutePath)) {
                    $this->start($phar, $absolutePath . DIRECTORY_SEPARATOR);
                    return;
                }

                !is_null($content = $this->getBin($relativePath, $absolutePath)) || !is_null($content = $this->getSrc($relativePath, $absolutePath)) ?
                $this->addCode($phar, $relativePath, $content) :
                $this->addFile($phar, $relativePath, $absolutePath);
            }
        }
    }
}