<?php namespace Skel;

class Parser {

  public function __construct(protected Lexer $lexer) {}

  public function parse_line(string $line) {
    $chars = str_split($line);
    $piece = '';
    foreach($chars as $char)
      $this->lexer->build($char, $piece);
    $this->parse($piece);
  }

  public function parse_file(string $file) {
    $fh = @fopen($file, 'r');
    $piece = '';
    while(!feof($fh))
      $this->lexer->build(fgetc($fh), $piece);
    $this->parse($piece);
    fclose($fh);
  }

  public function parse(string $code) {
    $this->lexer->build(' ', $code);
  }
}
