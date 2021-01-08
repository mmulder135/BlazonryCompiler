<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

class Term extends Node
{
    public function __construct(string $token, string $word)
    {
        $this->token = $token;
        $this->text = $word;
        $this->children = [];
    }

    public function hasChildToken(string $token): bool
    {
        return false;
    }

    public function getChildrenByToken(string $token): array
    {
        return [];
    }
}
