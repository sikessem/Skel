<?php

namespace Skel\Runtime;

class Grammar {
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
    ];

    public function terminals(): array {
        return $this->terminals;
    }

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

    public function non_terminals(): array {
        return $this->non_terminals;
    }
}