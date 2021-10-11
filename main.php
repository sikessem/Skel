<?php

use Skel\Runtime\Program;

require_once __DIR__ . '/cfg/bootstrap.php';

(new Program(getcwd()))->execute($argv, $argc);