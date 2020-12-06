<?php


namespace BlazonCompiler\Compiler\Lexer;

use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Language\Dictionary;
use BlazonCompiler\Compiler\Language\Separators;
use BlazonCompiler\Compiler\Language\Terminals;

class Lexer
{

    /** @var Dictionary */
    protected $terminals;
    /** @var Dictionary */
    protected $separators;
    /** @var array */
    protected $result;
    /** @var array */
    protected $tokens;

    /**
     * Lexer constructor.
     *
     * @param string $blazon
     * @throws LexerException
     */
    public function __construct(string $blazon)
    {
        $this->terminals = new Terminals();
        $this->separators = new Separators();

        $this->result = [];
        $this->tokens = [];

        $this->tokenize($blazon);
    }

    /**
     * Get the result of tokenizing, [string].
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Get the result of tokenizing, [Term].
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * Tokenize the entire blazon.
     *
     * @param string $blazon
     * @throws LexerException
     */
    protected function tokenize(string $blazon): void
    {
        $offset = 0;
        $len = strlen($blazon);
        $rollback = false;
        $space = false;
        // 'abc' => len = 3, blazon[0] = a, ..., blazon[2] = c
        while ($offset < $len) {
            if (!$space){
                if ($rollback) {
                    [$token, $word] = $this->matchString($blazon, $offset);
                    $rollback = false;
                } else try {
                    [$token, $word] = $this->match($blazon, $offset, $this->terminals);
                } catch (LexerException $e) {
                    //Try (another) space character
                    $space = true;
                    continue;
                }
            } else {
                try {
                    [$token, $word] = $this->match($blazon, $offset, $this->separators);
                } catch (LexerException $e) {
                    // There is no space when after the word, rollback
                    $offset -= $this->rollbackToken();
                    $rollback = true;
                    $space = false;
                    continue;
                }
            }
            // Save token
            $this->addToken($token, $word);
            $offset = $offset + strlen($word);
            $space = !$space;
        }
    }

    /**
     * Match the given string using the given dictionary, starting at offset.
     * Returns [$token, $word].
     *
     * @param string $blazon
     * @param int $offset
     * @param Dictionary $dictionary
     * @return array
     * @throws LexerException
     */
    protected function match(string $blazon, int $offset, Dictionary $dictionary): array
    {
        $match = preg_match($dictionary->getRegex(), $blazon, $matches, null, $offset);
        if (!$match) {
            throw new LexerException($blazon[$offset]);
        }
        // Get first match (second non-empty value in matches)
        $filteredMatches = array_filter($matches);
        $index = array_keys($filteredMatches)[1] - 1;
        $token = $dictionary->indexToToken($index);
        $word = $matches[0];
        return [$token, $word];
    }

    /**
     * Match next word to a string, in case normal tokenizing failed.
     *
     * @param string $blazon
     * @param int $offset
     * @return array
     * @throws LexerException
     */
    protected function matchString(string $blazon, int $offset): array
    {
        $match = preg_match($this->terminals->getStringRegex(), $blazon, $matches, null, $offset);
        if (!$match) {
            throw new LexerException($blazon[$offset]);
        }
        return [$this->terminals::STR, $matches[0]];
    }

    /**
     * Add a token to the result and tokenlist.
     * @param $token
     * @param $word
     */
    protected function addToken($token, $word): void
    {
        $this->result[] = new Term($token, $word);
        $this->tokens[] = $token;
    }

    protected function rollbackToken(): int
    {
        $term = array_pop($this->result);
        array_pop($this->tokens);
        return strlen($term->getWord());
    }
}
