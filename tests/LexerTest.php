<?php

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\Language\Separators;
use BlazonCompiler\Compiler\Language\Terminals;
use BlazonCompiler\Compiler\Lexer\Lexer;
use BlazonCompiler\Compiler\Lexer\LexerException;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /**
     * @test
     * @dataProvider basicWords
     * @dataProvider aFewTokens
     * @dataProvider simpleSentences
     * @param string $blazon
     * @param array $expectedTokens
     * @throws LexerException
     */
    public function checkTokenization(string $blazon, array $expectedTokens): void
    {
        $lexer = new Lexer($blazon);
        $tokens = $lexer->getTokens();
        $this->assertEqualS($expectedTokens, $tokens, "Failed on '{$blazon}'");
    }

    public function basicWords(): array
    {
        return [
            ["azure", [Terminals::TINCTURE]],
            ["argent", [Terminals::METAL]],
            ["bar", [Terminals::ORDINARY]],
            ["a", [Terminals::PREPOSITION]],
            ["s", [Terminals::STR]],
            ["asdf", [Terminals::STR]],
            [' ',[Separators::WS]],
            [',',[Separators::COMMA]],
        ];
    }

    public function aFewTokens(): array
    {
        return [
            [', ',[Separators::COMMA,Separators::WS]],
        ];
    }

    public function simpleSentences(): array
    {
        return [
            ['Azure a bar or', [Terminals::TINCTURE, Separators::WS, Terminals::PREPOSITION, Separators::WS,Terminals::ORDINARY, Separators::WS,Terminals::METAL]],
//            ['Azure, a bar or', [Terminals::TINCTURE, Separators::COMMA, Separators::WS, Terminals::PREPOSITION, Separators::WS,Terminals::ORDINARY, Separators::WS,Terminals::METAL]],
        ];
    }
}
