<?php

namespace BlazonCompiler\Compiler\Lexer;

use Exception;

class LexerException extends Exception
{

    /**
     * LexerException constructor.
     * @param string $character
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $character, int $code = 0, Exception $previous = null)
    {
        $message = "Lexer failed at character '{$character}'";
        parent::__construct($message, $code, $previous);
    }
}
