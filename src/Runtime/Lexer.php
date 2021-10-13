<?php

namespace Skel\Runtime;

class Lexer {
  public function __construct(array $lexemes) {
    $this->setLexemes($lexemes);
  }

  protected $lexemes = [];

  public function setLexemes(array $lexemes): static {
    foreach ($lexemes as $lexeme)
      $this->setLexeme($lexeme);
    return $this;
  }

  public function setLexeme(Lexeme $lexeme): static {
    $this->lexemes[] = $lexeme;
    return $this;
  }

  public function getLexemes(): ?array {
    return $this->lexemes;
  }

  public function getLexeme(string $code): ?Lexeme {
    foreach ($this->lexemes as $lexeme)
      if ($lexeme->is($code))
        return $lexeme;
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
      if ($lexeme = $this->getLexeme($statement)) {
          $this->token = $lexeme->getKey();
          $this->statement = $statement;
      } else {
          $this->add_statement_token() ? --$this->offset : $this->offset = 0;
          $this->process();
      }
    }
    return $this->add_statement_token();
  }

  protected ?string $lexeme = null;
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