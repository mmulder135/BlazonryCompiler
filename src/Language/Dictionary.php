<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class Dictionary
{

    /**
     * @var array<string, array<int, string>>
     * @phpstan-var array<mixed, array<int, string>>
     */
    protected array $dictionary;
    /** @var string  */
    protected string $regex;
    /** @var array<string> */
    protected array $tokensArray;

    public function __construct()
    {
        $this->createRegex();
    }

    /**
     * @return array<string, array<int, string>>
     * @phpstan-return array<mixed, array<int, string>>
     */
    public function getRules(): array
    {
        return $this->dictionary;
    }

    /**
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @param int $index
     * @return string
     */
    public function indexToToken(int $index): string
    {
        return $this->tokensArray[$index];
    }

    /**
     * @return string[]
     */
    public function getTokens(): array
    {
        return $this->tokensArray;
    }

    /**
     *
     */
    protected function createRegex(): void
    {
        $tokenMap = array();
        foreach ($this->dictionary as $name => $values) {
            $tokenMap[$name] = implode('|', $values);
        }
        $this->regex = '(\G(' . implode(')|\G(', array_values($tokenMap)) . '))';
        $this->tokensArray = array_keys($tokenMap);
    }
}
