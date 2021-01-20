<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

class Term extends Node
{
    /**
     * Term constructor.
     * @param string $token
     * @param string $word
     */
    public function __construct(string $token, string $word)
    {
        $this->token = $token;
        $this->text = $word;
        $this->children = [];
    }

    /**
     * @param string $token
     * @return false
     */
    public function hasChildToken(string $token): bool
    {
        return false;
    }

    /**
     * @param string $token
     * @return array []
     */
    public function getChildrenByToken(string $token): array
    {
        return [];
    }
}
