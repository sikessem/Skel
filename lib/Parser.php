<?php namespace Skel;

class Parser {

  public function __construct(protected array $keywords, protected array $patterns) {}

  public function parse_line(string $line) {
    $chars = str_split($line);
    $piece = '';
    foreach($chars as $char)
      $this->parse_piece($char, $piece);
  }

  public function parse_file(string $file) {
    $fh = @fopen($file, 'r');
    $piece = '';
    while(!feof($fh))
      $this->parse_piece(fgetc($fh), $piece);
    fclose($fh);
  }

  public function parse_piece(string $char, string &$piece) {
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
    if(in_array($piece, $this->keywords)) $is_valid = true;
    else foreach($this->patterns as $name => $pattern) {
      if(preg_match("/^$pattern$/", $piece)) {
        $is_valid = true;
        break;
      }
    } return $is_valid;
  }

  protected function is_space(string $char): bool {
    return (bool) preg_match('/^[ \r\n\t\s]$/', $char);
  }

  public function token(string $token) {
    echo "Token: $token" . PHP_EOL;
  }

  public function error(string $token) {
    fputs(STDERR, "Error: $token");
    exit(1);
  }
}
