<?php

namespace Skel\Runtime;

class Lexeme {
    public function __construct(protected string $type, protected int|string $key, protected mixed $value) {}

    public function getKey(): int|string {
        return $this->key;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getValue(): mixed {
        return $this->value;
    }

    public function is(string $value): bool {
        return match ($this->type) {
            'special' => $this->value === $value,
            'keyword' => strtolower($this->value) === strtolower($value),
            'pattern' => (bool) preg_match("/^$this->value$/", $value),
            'context' => $this->value['from'] === strtolower($value),
            default => false
        };
    }

    public function isNot(string $value): bool {
        return !$this->is($value);
    }

    public function in(string|array ...$values): bool {
        foreach ($values as $value)
            if (is_array($value)) {
                if ($this->in(...$value))
                    return true;
            }
            elseif ($this->is($value))
                return true;
        return false;
    }

    public function notIn(string|array ...$values): bool {
        return !$this->in(...$values);
    }
}
