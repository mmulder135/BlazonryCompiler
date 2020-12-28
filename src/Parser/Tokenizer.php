<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Parser;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Language\Combinator;
use BlazonCompiler\Compiler\Language\Separators;
use BlazonCompiler\Compiler\Language\Terminals;
use BlazonCompiler\Compiler\Language\UnrecognizedTokens;

class Tokenizer
{
    /** @var Combinator  */
    protected Combinator $dictionary;

    /**
     * Tokenizer constructor.
     */
    public function __construct()
    {
        $this->dictionary = new Combinator();
        $this->dictionary->addDictionary(new Terminals());
        $this->dictionary->addDictionary(new Separators());
        $this->dictionary->addDictionary(new UnrecognizedTokens());
    }


    /**
     * Tokenize the entire blazon.
     * @param string $blazon
     * @return IR
     */
    public function tokenize(string $blazon): IR
    {
        $blazon = mb_strtolower($blazon);
        $result = [];
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
            $result[(int) $offset] = new Term($token, $word);
            $offset += strlen($word);
        }
        return new IR($result);
    }
}
