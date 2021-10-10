<?php
use Skel\Runtime\Autoload;

require_once dirname(__DIR__) . '/src/Runtime/Autoload.php';

spl_autoload_register(new Autoload(dirname(__DIR__)), true, true);