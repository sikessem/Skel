<?php

namespace Skel\Runtime;

class Hacker {

  protected $terminals = [];
  
  protected $non_terminals = [];

  public function __construct(array $terminals = [], array $non_terminals = []) {
    $this->grammar = new Grammar;
    $this->setKeywords($terminals['keywords'] ?? [])
         ->setSpecials($terminals['specials'] ?? [])
         ->setPatterns($terminals['patterns'] ?? [])
         ->setNonTerminals($non_terminals);
  }

  protected Grammar $grammar;

  public function setTerminals(string $key, array $values): static {
    foreach ($values as $name => $value)
      $this->setTerminal($key, $name, $value);
    return $this;
  }

  public function setTerminal(string $key, string $name, string $value): static {
    if (isset($this->grammar->terminals()[$key][$name]))
      $this->terminals[$key][$name] = $value;
    return $this;
  }

  public function getTerminals(string $key): ?array {
    return $this->terminals[$key] ?? $this->grammar->terminals()[$key] ?? null;
  }

  public function getTerminal(string $key, string $name): ?string {
    return $this->terminals[$key][$name] ?? null;
  }

  public function setNonTerminals(array $non_terminals): static {
    foreach ($non_terminals as $name => $value)
      $this->setNonTerminal($name, $value);
    return $this;
  }

  public function setNonTerminal(string $name, string $value): static {
    if (isset($this->grammar->non_terminals()[$name]))
      $this->non_terminals[$name] = $value;
    return $this;
  }

  public function getNonTerminals(): ?array {
    return $this->non_terminals ?? $this->grammar->non_terminals() ?? null;
  }

  public function getNonTerminal(string $name): ?string {
    return $this->non_terminals[$name] ?? $this->grammar->non_terminals()[$name] ?? null;
  }

  public function setKeywords(array $keywords): static {
    return $this->setTerminals('keywords', $keywords);
  }

  public function setKeyword(string $name, string $value): static {
    return $this->setTerminal('keywords', $name, $value);
  }

  public function getKeywords(): array {
    return $this->getTerminals('keywords');
  }

  public function getKeyword(string $name): ?string {
    return $this->getTerminal('keywords', $name);
  }

  public function setSpecials(?array $specials = null): static {
    return $this->setTerminals('specials', $specials);
  }

  public function setSpecial(string $name, string $value): static {
    return $this->setTerminal('specials', $name, $value);
  }

  public function getSpecials(): array {
    return $this->getTerminals('specials');
  }

  public function getSpecial(string $name): ?string {
    return $this->getTerminal('specials', $name);
  }

  public function setPatterns(?array $patterns = null): static {
    return $this->setTerminals('patterns', $patterns);
  }

  public function setPattern(string $name, string $value): static {
    return $this->setTerminal('patterns', $name, $value);
  }

  public function getPatterns(): ?array {
    return $this->getTerminals('patterns');
  }

  public function getPattern(string $name): ?string {
    return $this->getTerminal('patterns', $name);
  }
}
