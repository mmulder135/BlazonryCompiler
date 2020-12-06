<?php


namespace BlazonCompiler\Compiler\AST;


abstract class Node
{
    /** @var string */
    protected $token;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

}
