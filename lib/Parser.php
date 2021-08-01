<?php namespace Skel;

class Parser {

  public function __construct(protected Lexer $lexer) {}

  public function parse_line(string $line) {
    $chars = str_split($line);
    $piece = '';
    foreach($chars as $char)
      $this->lexer->build($char, $piece);
    if($piece !== '')
      $this->lexer->build(' ', $piece);
  }

  public function parse_file(string $file) {
    $fh = @fopen($file, 'r');
    $piece = '';
    while(!feof($fh))
      $this->lexer->build(fgetc($fh), $piece);
    if($piece !== '')
      $this->lexer->build(' ', $piece);
    fclose($fh);
  }
}
