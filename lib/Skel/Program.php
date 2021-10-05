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
  }

  protected string $cwd;

  public function execute(array $argv, int $argc): void {
    if(php_sapi_name() === 'cli') {
      if($argc < 2) {
        fputs(STDOUT, 'Type "\q" to exit' . PHP_EOL);
        while('\q' !== ($line = readline('>')))
          $this->interpret_line($line);
      } else {
        $input = $argv[1] ?? '';
        while(!is_readable($file = $this->cwd . $input))
          $input = readline('Enter an existing file in the source directory : ');
        $this->interpret_file($file);
      }
      echo $this->statement . PHP_EOL;
      print_r($this->tokens);
    }
  }

  protected function interpret_dir(string $dir): bool {
    $files = scandir($dir);
    foreach ($files as $file)
      if (!in_array($file, ['.', '..']))
        if (!$this->interpret_file($dir . DIRECTORY_SEPARATOR . $file))
          return false;
    return true;
  }

  protected function interpret_file(string $file): bool {
    if (is_dir($file))
      return $this->interpret_dir($file);

    $lines = file($file);
    foreach ($lines as $line)
      if (!$this->interpret_line($line))
        return false;
    return true;
  }

  protected function interpret_line(string $line): bool {
    $chars = str_split($line);
    foreach ($chars as $char)
      if (!$this->interpret_char($char))
        return false;
    return true;
  }

  protected function interpret_char(string $char): bool {
    $statement = isset($this->statement) ? $this->statement . $char : $char;
    return $this->interpret($statement, $char);
  }

  protected function interpret(string $statement, ?string $char): bool {
    if ($this->match($statement)) {
      $this->statement = $statement;
      return true;
    } elseif (!empty($this->matches)) {
      if ($token = $this->token())
        $this->tokens[] = $token;
      $this->matches = [];
      $this->statement = null;
      return $this->interpret_char($char);
    } else {
      $this->matches = [];
      $this->statement = null;
      return false;
    }
  }

  protected const TOKENS = [
      'id' => '[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*',
      'float' => '\d+\.\d*',
      'digit' => '\d+',
      'blank' => '[ \t]+',
      'newLine' => '[\r\n]+',
      'space' => '[\s]+',
      'unknown' => '[^\S]+',
  ];

  protected ?string $statement = null;

  protected array $matches = [];

  protected function match(string $value): bool {
    foreach (self::TOKENS as $token => $pattern) {
      if (preg_match("/^$pattern$/", $value, $matches)) {
        $this->matches[$token][] = $value;
        return true;
      }
    }
    return false;
  }

  public function token(): ?array {
    $token_name = '';
    $token_value = '';
    foreach ($this->matches as $name => $values) {
      foreach ($values as $value) {
        if (strlen($value) > strlen($token_value)) {
          $token_name = $name;
          $token_value = $value;
        }
      }
    }
    return !empty($token_value) ? [$token_name, $token_value] : null;
  }

  protected array $tokens = [];
}
