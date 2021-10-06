<?php namespace Skel;

class Lexer {
  public function __construct() {
    $this->hacker = new Hacker;
  }

  protected Hacker $hacker;

  public function get_token(string $value): ?string {
    if ($keywords = $this->hacker->getKeywords())
      foreach ($keywords as $keyword)
        if (strtolower($keyword) === strtolower($value))
          return $keyword;

    if ($specials = $this->hacker->getSpecials())
      foreach ($specials as $token => $special)
        if ($special === $value)
          return $token;

    if ($patterns = $this->hacker->getPatterns())
      foreach ($patterns as $token => $pattern)
        if (preg_match("/^$pattern$/", $value, $matches))
          return $token;

    return null;
  }

  public function get_tokens(string $code): array {
    $this->tokens = [];
    $this->set_chars(str_split($code));
    $this->process();
    return $this->tokens;
  }

  protected function process(): bool {
    while (!is_null($char = $this->get_char())) {
        $statement = isset($this->statement) ? $this->statement . $char : $char;
        if ($token = $this->get_token($statement)) {
            $this->token = $token;
            $this->statement = $statement;
        } else {
            $this->add_statement_token() ? --$this->offset : $this->offset = 0;
            $this->process();
        }
    }
    return $this->add_statement_token();
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

  protected array $chars = [];
  protected int $offset = 0;

  protected function set_chars(array $chars): void {
    $this->chars = $chars;
    $this->offset = 0;
  }

  protected function get_char(): ?string {
    return isset($this->chars[$this->offset]) ? $this->chars[$this->offset++] : null;
  }
}