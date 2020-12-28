<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Parser;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\Language\Dictionary;
use BlazonCompiler\Compiler\Language\FieldMatches;
use BlazonCompiler\Compiler\Language\Tokens;
use BlazonCompiler\Compiler\Language\ShortMatches;

class Parser
{

    public function parse(string $blazon): IR
    {
        // Completely parse the blazon

        // level 1: tokenizer
        $tokenizer = new Tokenizer();
        $ir = $tokenizer->tokenize($blazon);

        // level 2: remove unwanted tokens
        $terms = $ir->getNodes();
        foreach ($terms as $offset => $term) {
            $token = $term->getToken();
            if (($token == Tokens::WS) || ($token == Tokens::AND)) {
                $ir->removeNode($offset);
            }
        }
        $ir->fixOffsets();

        // level 3: short matches
        $this->match($ir, new ShortMatches());


        // level 4: field matches
        $this->match($ir, new FieldMatches());

        return $ir;
    }

    /**
     * Do one loop through the IR with the given dictionary
     * @param IR $ir
     * @param Dictionary $dictionary
     * @return IR
     */
    public function match(IR $ir, Dictionary $dictionary): IR
    {
        $string = $ir->getString();
        $offset = 0;
        while (preg_match(
            $dictionary->getRegex(),
            $string,
            $matches,
            PREG_OFFSET_CAPTURE|PREG_UNMATCHED_AS_NULL,
            $offset
        )) {
            $filteredMatches = array_filter($matches, fn($v) => $v[0] != null);
            $index = ((int) array_keys($filteredMatches)[1] )- 1;
            $token = $dictionary->indexToToken($index);
            [$word,$offset] = $matches[0];
            $ir->addMatch($token, $word, $offset);
            $offset += strlen($word);
        }
        $ir->fixOffsets();
        return $ir;
    }
}
