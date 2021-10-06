<?php

namespace Skel;

class Interpreter {
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
        $this->set_chars(str_split($line . PHP_EOL));
        return $this->interpret();
    }

    protected function interpret(): bool {
        while (!is_null($char = $this->get_char())) {
            $statement = isset($this->statement) ? $this->statement . $char : $char;
            if ($token = $this->get_token($statement)) {
                $this->token = $token;
                $this->statement = $statement;
            } else {
                $this->add_statement_token() ? --$this->offset : $this->offset = 0;
                $this->interpret();
            }
        }
        return $this->add_statement_token();
    }

    protected array $chars = [];
    protected int $offset = 0;

    protected function set_chars(array $chars): void {
        $this->chars = $chars;
        $this->offset = 0;
    }

    protected function get_char(): ?string {
        return isset($this->chars[$this->offset]) ? $this->chars[$this->offset++] : null;
    }

    protected const TOKENS = [
        'id'      => '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*',
        'float'   => '\d+\.\d*',
        'digit'   => '\d+',
        'blank'   => '[ \t]+',
        'newLine' => '[\r\n]+',
        'space'   => '[\s]+',
        'unknown' => '[^\S]+',
    ];

    public function get_token(string $value): ?string {
        foreach (self::TOKENS as $token => $pattern)
            if (preg_match("/^$pattern$/", $value, $matches))
                return $token;
        return null;
    }

    protected ?string $token = null;
    protected ?string $statement = null;
    protected array $tokens = [];

    protected function add_statement_token(): bool {
        if (isset($this->statement)) {
            $this->tokens[] = [$this->token, $this->statement];
            $this->statement = null;
            $this->token = null;
            return true;
        }
        return false;
    }

    public function get_tokens(): array {
        return $this->tokens;
    }
}