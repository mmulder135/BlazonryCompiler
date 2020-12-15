<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

abstract class Node
{
    protected string $token;
    protected string $text;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
