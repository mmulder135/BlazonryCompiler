<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\AST;

class NonTerm extends Node
{
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
        foreach ($children as $child) {
            if ($this->text == '') {
                $this->text = $child->getText();
            } else {
                $this->text .= ' '.$child->getText();
            }
        }
    }

    public function hasChildToken(string $token): bool
    {
        foreach ($this->children as $node) {
            if ($node->getToken() == $token) {
                return true;
            }
        }
        return false;
    }

    public function getChildrenByToken(string $token): array
    {
        $result = [];
        foreach ($this->children as $node){
            if ($node->getToken() == $token) {
                $result[] = $node;
            }
        }
        return $result;
    }

    public function getFirst(string $token): ?Node
    {
        foreach ($this->children as $node){
            if ($node->getToken() == $token) {
                return $node;
            }
        }
        return null;
    }
}
