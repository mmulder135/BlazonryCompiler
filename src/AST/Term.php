<?php


namespace BlazonCompiler\Compiler\AST;


class Term extends Node
{
    /** @var string */
    protected $word;

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
