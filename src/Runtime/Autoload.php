<?php

namespace Skel\Runtime;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Dir.php';

class Autoload {
    use Dir;

    protected const VERSION_OPERATOR_INVERSE = [
        '<' => '>=',
        'lt' => 'ge',
        '<=' => '>',
        'le' => 'gt',
        '>' => '<=',
        'gt' => 'le',
        '>=' => '<',
        'ge' => 'lt',
        '==' => '!=',
        '=' => '<>',
        'eq' => 'ne',
        '!=' => '==',
        '<>' => '=',
        'ne' => 'eq',
    ];

    public function __construct(string $dir, protected array $requirements) {
        $this->setDir($dir);
    }

    public function __invoke($object) {
        $plateform = $this->requirements['plateform'] ?? [];
        $plateform_version = $plateform['version'] ?? '>=' . PHP_VERSION;
        $plateform_extensions = $plateform['extensions'] ?? [];

        foreach (self::VERSION_OPERATOR_INVERSE as $version_operator => $operator_inverse) {
            if (preg_match("/^({$version_operator})([0-9]+(?:\.[0-9]+){0,3})$/", $plateform_version, $version_matches)) {
                $version_operator = $version_matches[1];
                $plateform_version = $version_matches[2];
                if (!version_compare(PHP_VERSION, $plateform_version, $version_operator)) {
                    fprintf(STDERR, "PHP version %s %s %s" . PHP_EOL, PHP_VERSION, $operator_inverse, $plateform_version);
                    exit(1);
                }
                break;
            }
            unset($version_matches);
        }
        unset($version_operator, $operator_inverse);

        foreach ($plateform_extensions as $extension_name => $extension_options) {        
            if (!extension_loaded($extension_name)) {
                fwrite(STDERR, "Load the $extension_name extension" . PHP_EOL);
                exit(1);
            }

            foreach ($extension_options as $option => $value) {
                if ((string) $value !== ini_get($option)) {
                    fwrite(STDERR, "Set the option $option to " . (is_bool($value) ? ($value === true ? 'On': 'Off') : $value) . ' from the php.ini file' . PHP_EOL);
                    exit(1);
                }
            }
            unset($option, $value);
        }
        unset($extension_name, $extension_options);


        // src autoload

        if (
            preg_match('/^'. preg_quote('Skel\\', '/') .'(.*)$/', $object, $matches) &&
            is_file($file = $this->dir . str_replace('\\', DIRECTORY_SEPARATOR, "\\src\\$matches[1].php"))
        ) return require_once $file;


        // lib autoload

        $dir = $this->dir . DIRECTORY_SEPARATOR .'lib' . DIRECTORY_SEPARATOR;
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
