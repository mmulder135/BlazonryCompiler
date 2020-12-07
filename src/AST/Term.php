<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

class Term extends Node
{
    protected string $word;

    public function __construct(string $token, string $word)
    {
        $this->token = $token;
        $this->word = $word;
    }

    public function getWord(): string
    {
        return $this->word;
    }
}
