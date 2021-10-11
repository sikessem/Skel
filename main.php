<?php

use Skel\Runtime\Token;

const SKEL_PHAR = 'skel.phar';

const SKEL_SAPI = [
    'cli',
    'phpdbg'
];

require_once root() . 'cfg/boot.php';

function main(string $wdir, array $argv, int $argc, $input_stream, $output_stream, $error_stream): void {
    $phar_file = $argc > 1 && strpos($argv[1], '-') !== 0 ? $argv[1] : SKEL_PHAR;

    if (is_file($phar_file = $wdir . DIRECTORY_SEPARATOR . $phar_file) && !in_array('-y', $argv)) {
        fwrite($output_stream, "$phar_file already exists. Replace it (Y/n)? ");
        $response = '';
        while (!in_array($response, ['y', 'Y', 'n', 'N']))
            $response = fgetc($input_stream);
        if (in_array($response, ['n', 'N'])) exit;
        unlink($phar_file);
    }

    if (!in_array(PHP_SAPI, (array) SKEL_SAPI)) {
        fprintf($error_stream, '%s is not a PHP CLI setup' . PHP_EOL, PHP_BINARY);
        exit(1);
    }

    $skel_phar = phar(new \Phar($phar_file, 0, 'skel.phar'));
    setup(\Phar::SHA512, stub());
    unset($skel_phar);
}

function root(): string {
    return __DIR__ . DIRECTORY_SEPARATOR;
}

function phar(?\Phar $phar = null): ?\Phar {
    static $_phar;
    return isset($phar) ? ($_phar = $phar) : ($_phar ?? null);
}

function stub(): string {
    $stub = <<<'EOF'
#!/usr/bin/env php
<?php

if (!class_exists('Phar')) {
    echo 'PHP\'s archive (Phar) extension is missing.' . PHP_EOL;
    exit(1);
}

Phar::mapPhar('skel.phar');

require 'phar://skel.phar/bin/skel';

__HALT_COMPILER();
EOF;
    return $stub;
}

function setup(int $algo, string $stub): void {
    $phar = phar();
    $phar->startBuffering();
    $phar->setSignatureAlgorithm($algo);
    start(root());
    $phar->setStub($stub);
    $phar->stopBuffering();
}

function strip(string $code): string {
    if (!function_exists('token_get_all'))
        return $code;

    $tokens = token_get_all($code);
    $output = '';
    $previous = new Token;
    $next = new Token;
    foreach ($tokens as $key => $token) {
        $next->setValue($tokens[$key + 1] ?? null);
        $token = new Token($token);
        if ($token->isString())
            $output .= $token;
        elseif ($token->in(T_COMMENT, T_DOC_COMMENT))
            $output .= '';
        elseif ($token->is(T_WHITESPACE)) {
            if (
                (
                    $previous->isNotArray() ||
                    $next->isNotArray()
                ) || (
                    $previous->is(T_DOUBLE_ARROW) ||
                    $next->is(T_DOUBLE_ARROW)
                ) ||(
                    $previous->in(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT) ||
                    $next->in(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT)
                ) || (
                    $previous->is(T_OPEN_TAG) ||
                    $next->is(T_CLOSE_TAG)
                )
            ) $output .= '';
            else {
                $space = $token->getContent();
                $space = preg_replace('/[ \t]+/', ' ', $space);
                $space = preg_replace('/[\r\n]+/', "\n", $space);
                $space = preg_replace('/\n +/', "\n", $space);
                $space = preg_replace('/\s+/s', ' ', $space);
                $output .= $space;
            }
        }
        else
            $output .= $token;
        $previous = $token;
    }
    unset($previous, $next, $tokens);
    return $output;
}

function path(string $path): string {
    $realpath = realpath($path);
    $pos = strpos($realpath, root());
    $path = (false !== $pos) ? substr_replace($realpath, '', $pos, strlen(root())) : $realpath;
    $path = strtr($path, '\\', '/');
    return $path;
}

function ignoreFile(string $file): bool {
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

function ignorePath(string $path): bool {
    foreach ([
        '^pkg\/',
        '^.git\/',
    ] as $ignore)
        if (preg_match("/$ignore/", $path))
            return true;
    return false;
}

function getBin(string $relativePath, string $absolutePath): ?string {
    return
        fnmatch('bin/*', $relativePath) &&
        ($content = file_get_contents($absolutePath)) &&
        $content !== ($replacement = preg_replace('/^\#\!\/usr\/bin\/env php\s*/', '', $content)) ?
            $replacement:
            null;
}

function getSrc(string $relativePath, string $absolutePath): ?string {
    return fnmatch('*.php', $relativePath) ? file_get_contents($absolutePath) : null;
}

function addCode(string $path, string $content, bool $strip = true): void {
    echo "Add code $path" . PHP_EOL;
    phar()->addFromString($path, $strip ? strip($content) : $content);
}

function addFile(string $relativePath, string $absolutePath): void {
    echo "Add file $absolutePath as $relativePath" . PHP_EOL;
    phar()->addFile($absolutePath, $relativePath);
}

function start(string $dir): void {
    if ($names = scandir($dir))
        foreach ($names as $name)
            build($dir, $name);
}

function build(string $dir, string $file): void {
    if (!in_array($file, ['.', '..'])) {
        $absolutePath = $dir . $file;
        $relativePath = path($absolutePath);

        if (!ignoreFile($relativePath) && !ignorePath($relativePath)) {
            if (is_dir($absolutePath)) {
                start($absolutePath . DIRECTORY_SEPARATOR);
                return;
            }

            !is_null($content = getBin($relativePath, $absolutePath)) || !is_null($content = getSrc($relativePath, $absolutePath)) ? addCode($relativePath, $content) : addFile($relativePath, $absolutePath);
        }
    }
}

main(getcwd(), $argv, $argc, STDIN, STDOUT, STDERR);