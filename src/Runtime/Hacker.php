<?php

namespace Skel\Runtime;

class Hacker {
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
}
