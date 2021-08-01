<?php namespace Skel;

class Lexer {

  public function __construct(protected array $terminals, protected array $non_terminals) {}

  public function build(string $char, string &$piece) {
    if($this->is_space($char)) {
      if($piece === '') return;
      $this->is_valid($piece)?
      $this->token($piece):
      $this->error($piece);
      $piece = '';
      return;
    } $piece .= $char;
  }

  public function is_valid(string $piece): bool {
    $is_valid = false;
    if(in_array($piece, $this->terminals['keywords'])) $is_valid = true;
    else foreach($this->terminals['patterns'] as $name => $pattern) {
      if(preg_match("/^$pattern$/", $piece)) {
        $is_valid = true;
        break;
      }
    } return $is_valid;
  }

  public function token(string $token) {
    echo "Token: $token" . PHP_EOL;
  }

  public function error(string $token) {
    fputs(STDERR, "Error: $token");
    exit(1);
  }

  protected function is_space(string $char): bool {
    return (bool) preg_match('/^[ \r\n\t\s]$/', $char);
  }

}
