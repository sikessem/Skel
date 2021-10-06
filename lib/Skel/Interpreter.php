<?php

namespace Skel;

class Interpreter {
    public function __construct() {
        $this->lexer = new Lexer;
    }

    protected Lexer $lexer;

    public function interpret_dir(string $dir): bool {
        $files = scandir($dir);
        foreach ($files as $file)
            if (!in_array($file, ['.', '..']))
                if (!$this->interpret_file($dir . DIRECTORY_SEPARATOR . $file))
                    return false;
        return true;
    }

    public function interpret_file(string $file): bool {
        if (is_dir($file))
            return $this->interpret_dir($file);

        $lines = file($file);
        foreach ($lines as $line)
            if (!$this->interpret_line($line))
                return false;  
        return true;
    }

    public function interpret_line(string $line): bool {
        return $this->interpret($line . PHP_EOL);
    }

    public function interpret(string $code): bool {
        $tokens = $this->lexer->get_tokens($code);
        print_r($tokens);
        return !empty($tokens);
    }
}