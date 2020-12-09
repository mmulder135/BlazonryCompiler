<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Lexer;

use BlazonCompiler\Compiler\AST\Node;
use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Language\Combinator;
use BlazonCompiler\Compiler\Language\Separators;
use BlazonCompiler\Compiler\Language\Terminals;

class Lexer
{
    /** @var Combinator  */
    protected Combinator $dictionary;
    /** @var array<Term>  */
    protected array $result;
    /** @var array<string>  */
    protected array $tokens;

    /**
     * Lexer constructor.
     *
     * @param string $blazon
     */
    public function __construct(string $blazon)
    {
        $this->dictionary = new Combinator();
        $this->dictionary->addDictionary(new Terminals());
        $this->dictionary->addDictionary(new Separators());

        $this->result = [];
        $this->tokens = [];

        $this->tokenize($blazon);
    }

    /**
     * Get the result of tokenizing.
     * @return array<string>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Get the result of tokenizing.
     * @return array<Node>
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * Tokenize the entire blazon.
     *
     * @param string $blazon
     */
    protected function tokenize(string $blazon): void
    {
        $offset = 0;
        while (preg_match(
            $this->dictionary->getRegex(),
            $blazon,
            $matches,
            PREG_OFFSET_CAPTURE|PREG_UNMATCHED_AS_NULL,
            $offset
        )) {
            $filteredMatches = array_filter($matches, fn($v) => $v[0] != null);
            $index = ((int) array_keys($filteredMatches)[1] )- 1;
            $token = $this->dictionary->indexToToken($index);
            [$word,$offset] = $matches[0];
            $this->addToken($token, $word);
            $offset += strlen($word);
//            $blazon = substr_replace($blazon, $token, $offset, strlen($word));
        }
    }

    /**
     * Add a token to the result and tokenlist.
     * @param string $token
     * @param string $word
     */
    protected function addToken(string $token, string $word): void
    {
        $this->result[] = new Term($token, $word);
        $this->tokens[] = $token;
    }
}
