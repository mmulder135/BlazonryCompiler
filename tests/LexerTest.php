<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\Language\Separators;
use BlazonCompiler\Compiler\Language\Terminals;
use BlazonCompiler\Compiler\Lexer\Lexer;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /**
     * @test
     * @dataProvider basicWords
     * @dataProvider commaCombinations
     * @dataProvider simpleSentences
     * @dataProvider forbiddenTokens
     * @param string $blazon
     * @param array<string> $expectedTokens
     */
    public function checkTokenization(string $blazon, array $expectedTokens): void
    {
        $lexer = new Lexer($blazon);
        $tokens = $lexer->getTokens();
        $this->assertEqualS($expectedTokens, $tokens, "Failed on '{$blazon}'");
    }

    /**
     * @phpstan-return array<int, array<int, array<int, string>|string>>
     */
    public function basicWords(): array
    {
        return [
            ["azure", [Terminals::TINCTURE]],
            ["argent", [Terminals::METAL]],
            ["bar", [Terminals::ORDINARY]],
            ["a", [Terminals::ONE]],
            ["s", []],
            ["asdf", []],
            ["barargent", []],
            [',',[Separators::COMMA]],
            ['dancetty',[Terminals::PARTITION_LINE]],
//            ["\n",[Separators::WS]], //Newline need " does NOT work with '
        ];
    }

    /**
     * There were some bugs with comma's
     * @phpstan-return array<int, array<int, array<int, string>|string>>
     */
    public function commaCombinations(): array
    {
        return [
            [', ',[Separators::COMMA]],
            ['argent,', [Terminals::METAL,Separators::COMMA]],
            [',argent,', [Separators::COMMA,Terminals::METAL,Separators::COMMA]]
        ];
    }

    /**
     * @phpstan-return array<int, array<int, array<int, string>|string>>
     */
    public function simpleSentences(): array
    {
        return [
            ['Azure a bar or',
                [
                    Terminals::TINCTURE,
                    Terminals::ONE,
                    Terminals::ORDINARY,
                    Terminals::METAL
                ]
            ],
            ['Azure, a bar or',
                [
                    Terminals::TINCTURE,
                    Separators::COMMA,
                    Terminals::ONE,
                    Terminals::ORDINARY,
                    Terminals::METAL
                ]
            ],
            ['asdf engrailed', [Terminals::PARTITION_LINE]],
        ];
    }

    /**
     * @phpstan-return array<int, array<int, array<int, string>|string>>
     */
    public function forbiddenTokens(): array
    {
        return [
            ['/',[]],
            ['\\',[]],
        ];
    }
}
