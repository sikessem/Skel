<?php namespace Skel;

class Program {

  public function __construct(string $cwd) {
    if(empty($cwd))
      throw new Error('Empty cwd given', Error::EMPTY_VALUE);

    if(in_array($cwd, ['.', '..']) || preg_match('/^\.{1,2}(\/|\\\\)/U', $cwd))
      throw new Error('Give an absolute path', Error::BAD_PATH);

    if(!is_dir($cwd))
      throw new Error("No such directory $cwd", Error::NO_DIRECTORY);

    if(!is_readable($cwd))
      throw new Error("Cannot read $cwd", Error::UNREADABLE);

    $this->cwd = realpath($cwd) . DIRECTORY_SEPARATOR;
    $this->parser = new Parser(Hacker::KEYWORDS, Hacker::PATTERNS);
  }

  protected string $cwd;

  public function main(array $argv, int $argc): void {
    if(php_sapi_name() === 'cli') {
      if($argc < 2) {
        while('\q' !== ($line = readline('>')))
          $this->parser->parse_line($line);
      } else {
        $input = $argv[1] ?? '';
        while(!is_readable($file = $this->cwd . $input))
          $input = readline('Enter an existing file in the source directory : ');
        $this->parser->parse_file($file);
      }
    }
  }
}
