<?php

use Skel\Runtime\Program;

require_once __DIR__ . '/cfg/bootstrap.php';

function main(string $wdir, array $argv, int $argc): void {
    if (in_array(PHP_SAPI, ['cli', 'phpdbg'], true))
        (new Program($wdir))->execute($argv, $argc);
}