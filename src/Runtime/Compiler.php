<?php

namespace Skel\Runtime;

class Compiler {
    public static function compileFile(string $file): string {
        $code = file_get_contents($file);
        return self::compileCode($code);
    }

    public static function compileCode(string $code): string {
        if (!function_exists('token_get_all'))
            return $code;

        $tokens = token_get_all($code);
        $output = '';
        $previous = new Token;
        $next = new Token;
        foreach ($tokens as $key => $token) {
            $next->setValue($tokens[$key + 1] ?? null);
            $token = new Token($token);
            if ($token->isString())
                $output .= $token;
            elseif ($token->in(T_COMMENT, T_DOC_COMMENT))
                $output .= '';
            elseif ($token->is(T_WHITESPACE)) {
                if (
                    (
                        $previous->isNotArray() ||
                        $next->isNotArray()
                    ) || (
                        $previous->is(T_DOUBLE_ARROW) ||
                        $next->is(T_DOUBLE_ARROW)
                    ) ||(
                        $previous->in(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT) ||
                        $next->in(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT)
                    ) || (
                        $previous->is(T_OPEN_TAG) ||
                        $next->is(T_CLOSE_TAG)
                    )
                ) $output .= '';
                else {
                    $space = $token->getContent();
                    $space = preg_replace('/[ \t]+/', ' ', $space);
                    $space = preg_replace('/[\r\n]+/', "\n", $space);
                    $space = preg_replace('/\n +/', "\n", $space);
                    $space = preg_replace('/\s+/s', ' ', $space);
                    $output .= $space;
                }
            }
            else
                $output .= $token;
            $previous = $token;
        }
        unset($previous, $next, $tokens);
        return $output;
    }
}