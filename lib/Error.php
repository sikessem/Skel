<?php namespace Skel;

class Error extends \Exception {

  const EMPTY_VALUE = 0x01;

  const BAD_PATH = 0x02;

  const NO_DIRECTORY = 0x04;

  const UNREADABLE = 0x08;
}
