<?php

namespace Skel\Runtime;

class Autoload {
    public function __construct(protected string $root) {}

    public function __invoke($object) {
        // src autoload

        if (
            preg_match('/^'. preg_quote('Skel\\', '/') .'(.*)$/', $object, $matches) &&
            is_file($file = $this->root . str_replace('\\', DIRECTORY_SEPARATOR, "\\src\\$matches[1].php"))
        ) return require_once $file;


        // lib autoload

        $dir = $this->root . DIRECTORY_SEPARATOR .'lib' . DIRECTORY_SEPARATOR;
        if (is_file($file = $dir . str_replace('\\', DIRECTORY_SEPARATOR, "$object.php"))) return require_once $file;

        $names = explode('\\', $object);
        foreach ($names as $key => $name) {
            unset($names[$key]);

            if (empty($names)) {
                if (
                    is_file($file = $dir . $name . '.php') ||
                    is_file($file = $dir . 'src' . DIRECTORY_SEPARATOR . "$name.php")
                ) return require_once $file;
            }
            
            if (
                is_file($file = $dir . $name . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $names) . '.php') ||
                is_file($file = $dir . $name . DIRECTORY_SEPARATOR . 'src'. DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $names) . '.php')
            ) return require_once $file;
            $dir .= $name . DIRECTORY_SEPARATOR;
        }
    }
}
