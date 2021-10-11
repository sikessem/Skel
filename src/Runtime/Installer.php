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

        $fileFinder = new FileFinder;
        $fileFinder->exclude([
            '.gitignore',
            'LICENSE',
            'README.md',
            'composer.*',
            'install',
            'skel.phar',
        ]);
        $pathFinder = new PathFinder;
        $pathFinder->exclude([
            '/^pkg\//',
            '/^.git\//',
        ]);

        $archiver = new Archiver($phar, $this->dir, [$fileFinder, $pathFinder]);
        $archiver->archive();

        $phar->setStub(Compiler::compileCode($stub));
        $phar->stopBuffering();

        if (isset($algo))
            $phar->setSignatureAlgorithm($algo);

        $phar->startBuffering();
        $phar->setStub(Compiler::compileCode($stub));
        $phar->stopBuffering();
    }
}