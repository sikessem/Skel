<?php

namespace Skel\Runtime;

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
  }

  protected string $cwd;

  public function execute(array $argv, int $argc): void {
    if(php_sapi_name() === 'cli') {
      $interpreter = new Interpreter;
      if($argc < 2) {
        fputs(STDOUT, 'Type "\q" to exit' . PHP_EOL);
        while('\q' !== ($line = readline('>')))
          $interpreter->interpret_line($line);
      } else {
        $input = $argv[1] ?? '';
        while(!is_readable($file = $this->cwd . $input))
          $input = readline('Enter an existing file in the source directory : ');
        $interpreter->interpret_file($file);
      }
    }
  }
}