<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

class NonTerm extends Node
{
    /** @var array<Node>  */
    protected array $children;

    /**
     * NonTerm constructor.
     * @param string $token
     * @param array<Node> $children
     */
    public function __construct(string $token, array $children)
    {
        $this->token = $token;
        $this->children = $children;
        $this->text = '';
//        foreach ($children as $child) {
//            $this->text .= $child->getText();
//        }
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
