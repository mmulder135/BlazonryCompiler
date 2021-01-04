<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

abstract class Node
{
    /** @var string  */
    protected string $token;
    /** @var string  */
    protected string $text;
    /** @var array<Node>  */
    protected array $children;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
