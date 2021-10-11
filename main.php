<?php

use Skel\Runtime\Program;

require_once __DIR__ . '/cfg/boot.php';

(new Program(getcwd()))->execute($argv, $argc);