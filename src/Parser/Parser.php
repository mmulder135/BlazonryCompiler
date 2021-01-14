<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Parser;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\Language\Dictionary;
use BlazonCompiler\Compiler\Language\Ordinaries;
use BlazonCompiler\Compiler\Language\ShortMatches;

class Parser
{

    public static function parse(string $blazon): IR
    {
        // Completely parse the blazon

        // level 1: tokenizer
        $tokenizer = new Tokenizer();
        $ir = $tokenizer->tokenize($blazon);

        // level 2: short matches
        self::match($ir, new ShortMatches());

        // level 3 : find and parse field declaration
        // Blazon should start with field declaration
        FieldMatcher::parseField($ir);

        self::match($ir, new Ordinaries());

        return $ir;
    }

    /**
     * Do one loop through the IR with the given dictionary
     * @param IR $ir
     * @param Dictionary $dictionary
     * @return bool
     */
    public static function match(IR $ir, Dictionary $dictionary): bool
    {
        $string = $ir->getString();
        $offset = 0;
        $foundMatch = false;
        while (preg_match(
            $dictionary->getRegex(),
            $string,
            $matches,
            PREG_OFFSET_CAPTURE|PREG_UNMATCHED_AS_NULL,
            $offset
        )) {
            $foundMatch = true;
            $filteredMatches = array_filter($matches, fn($v) => $v[0] != null);
            $index = ((int) array_keys($filteredMatches)[1] )- 1;
            $token = $dictionary->indexToToken($index);
            [$word,$offset] = $matches[0];
            $ir->addMatch($token, $word, $offset);
            $offset += strlen($word);
        }
        $ir->fixOffsets();
        return $foundMatch;
    }
}
