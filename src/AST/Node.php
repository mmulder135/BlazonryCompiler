<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

abstract class Node
{
    protected string $token;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
