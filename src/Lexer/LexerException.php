<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Lexer;

use Exception;

class LexerException extends Exception
{

    /**
     * LexerException constructor.
     * @param string $string
     * @param int $offset
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $string, int $offset, int $code = 0, Exception $previous = null)
    {
        $remaining = substr($string, $offset);
        $format = 'Lexer failed at offset %d, remaining input: %s';
        $message = sprintf($format, $offset, $remaining);
        parent::__construct($message, $code, $previous);
    }
}
