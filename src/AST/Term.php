<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

class Term extends Node
{
    public function __construct(string $token, string $word)
    {
        $this->token = $token;
        $this->text = $word;
    }
}
