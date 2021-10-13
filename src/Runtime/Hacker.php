<?php

namespace Skel\Runtime;

class Hacker {
  protected $terminals = [
    'keywords' => [
      'let',
      'set',
      'get',
      'put',
      'check',
      'isset',
      'unset',
      'reset',
    ],

    'specials' => [
      'add' => '+',
      'sub' => '-',
      'mul' => '*',
      'div' => '/',
      'rem' => '%',
      'exp' => '^',
      'colon' => ':',
      'equal' => '=',
      'comma' => ',',
      'semicolon' => ';',
    ],

    'patterns' => [
      'id' => '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*',
      'float' => '\d+\.\d*',
      'digit' => '\d+',
      'blank' => '[ \t]+',
      'newLine' => '[\r\n]+',
      'space' => '[\s]+',
      'unknown' => '[^\S]+',
    ],

    'contexts' => [
      'note' => [
        'from' => '<!',
        'to' => '!>',
        'escape' => false,
      ],
      'code' => [
        'from' => '<?',
        'to' => '?>',
        'escape' => false,
      ],
      'data' => [
        'from' => '`',
        'to' => '`',
      ],
      'byte' => [
        'from' => '\'',
        'to' => '\'',
      ],
      'text' => [
        'from' => '"',
        'to' => '"',
      ],
    ]
  ];

  protected $non_terminals = [
    '' => '{Statement}',
    'Statement' => '(Let|Put)&";"',
    'Let' => '"let"&space&Declaration',
    'Put' => '"put"&space&Expression',
    'Declaration' => 'ConstantDefinition|VariableDeclaration|ValueAssignment',
    'ConstantDefinition' => 'id&":"&Value',
    'VariableDeclaration' => 'id&"="&Value',
    'ValueAssignment' => 'id&":="&Value',
    'Expression' => 'Addition|Substraction|Multiplication|Division|Modulo|Exponent|Value',
    'Addition' => 'Expression&"+"&Expression',
    'Substraction' => 'Expression&"-"&Expression',
    'Multiplication' => 'Expression&"*"&Expression',
    'Division' => 'Expression&"/"&Expression',
    'Modulo' => 'Expression&"%"&Expression',
    'Exponent' => 'Expression&"^"&Expression',
    'Value' => 'Number',
    'Number' => 'digit|float',
  ];

  public function setTerminals(string $key, array $values): static {
    foreach ($values as $name => $value)
      $this->setTerminal($key, $name, $value);
    return $this;
  }

  public function setTerminal(string $key, string $name, string $value): static {
    if (isset($this->terminals[$key][$name]))
      $this->terminals[$key][$name] = $value;
    return $this;
  }

  public function getTerminals(string $key): ?array {
    return $this->terminals[$key] ?? null;
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
    if (isset($this->non_terminals[$name]))
      $this->non_terminals[$name] = $value;
    return $this;
  }

  public function getNonTerminals(): ?array {
    return $this->non_terminals ?? $this->non_terminals ?? null;
  }

  public function getNonTerminal(string $name): ?string {
    return $this->non_terminals[$name] ?? $this->non_terminals[$name] ?? null;
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

  public function setContexts(?array $contexts = null): static {
    return $this->setTerminals('contexts', $contexts);
  }

  public function setContext(string $name, string $value): static {
    return $this->setTerminal('contexts', $name, $value);
  }

  public function getContexts(): ?array {
    return $this->getTerminals('contexts');
  }

  public function getContext(string $name): ?string {
    return $this->getTerminal('contexts', $name);
  }
}
