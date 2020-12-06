<?php


namespace BlazonCompiler\Compiler\Language;

class Dictionary
{
    /** @var array */
    protected $dictionary;
    /** @var string */
    protected $regex;
    /** @var array */
    protected $tokensArray;

    public function __construct()
    {
        $this->createRegex();
    }

    public function getRules(): array
    {
        return $this->dictionary;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function indexToToken(int $index): string
    {
        return $this->tokensArray[$index];
    }

    public function getTokens(): array
    {
        return $this->tokensArray;
    }

    protected function createRegex(): void
    {
        $tokenMap = array();
        foreach ($this->dictionary as $name => $values) {
            $tokenMap[$name] = implode('|', $values);
        }
        $this->regex = '((\G' . implode(')|(\G', array_values($tokenMap)) . '))';
        $this->tokensArray = array_keys($tokenMap);
    }

}
