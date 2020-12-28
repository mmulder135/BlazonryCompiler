<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\Language\Tokens;
use BlazonCompiler\Compiler\Parser\Tokenizer;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /**
     * @test
     * @dataProvider basicWords
     * @dataProvider commaCombinations
     * @dataProvider simpleSentences
     * @dataProvider invalidTokens
     * @param string $blazon
     * @param array<string> $expectedTokens
     */
    public function checkTokenization(string $blazon, array $expectedTokens): void
    {
        $tokenizer = new Tokenizer();
        $ir = $tokenizer->tokenize($blazon);
        $result = array_values(array_map(function ($node) {
            return $node->getToken();
        }, $ir->getNodes()));
        $this->assertEqualS($expectedTokens, $result, "Failed on '{$blazon}'");
    }

    /**
     * @phpstan-return array<int, array<int, array<int, string>|string>>
     */
    public function basicWords(): array
    {
        return [
            ["azure", [Tokens::TINCTURE]],
            ["argent", [Tokens::METAL]],
            ["bar", [Tokens::ORDINARY]],
            ["a", [Tokens::ONE]],
            ["s", [Tokens::STR]],
            ["asdf", [Tokens::STR]],
            ["barargent", [Tokens::STR]],
            [',',[Tokens::COMMA]],
            ['dancetty',[Tokens::PARTITION_LINE]],
        ];
    }

    /**
     * There were some bugs with comma's
     * @phpstan-return array<int, array<int, array<int, string>|string>>
     */
    public function commaCombinations(): array
    {
        return [
            [', ',
                [
                    Tokens::COMMA,
                ]
            ],
            ['argent,',
                [
                    Tokens::METAL,
                    Tokens::COMMA
                ]
            ],
            [',argent,',
                [
                    Tokens::COMMA,
                    Tokens::METAL,
                    Tokens::COMMA
                ]
            ]
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
                    Tokens::TINCTURE,
                    Tokens::ONE,
                    Tokens::ORDINARY,
                    Tokens::METAL
                ]
            ],
            ['Azure, a bar or',
                [
                    Tokens::TINCTURE,
                    Tokens::COMMA,
                    Tokens::ONE,
                    Tokens::ORDINARY,
                    Tokens::METAL
                ]
            ],
            ['asdf engrailed',
                [
                    Tokens::STR,
                    Tokens::PARTITION_LINE
                ]
            ],
            ['per bend azure and gules',
                [
                    Tokens::PER,
                    Tokens::ORDINARY,
                    Tokens::TINCTURE,
                    Tokens::AND,
                    Tokens::TINCTURE
                ]
            ],
        ];
    }

    /**
     * @phpstan-return array<int, array<int, array<int, string>|string>>
     */
    public function invalidTokens(): array
    {
        return [
            ['/',[]],
            ['\\',[]],
            [' ',[]],
            ["\n",[]],
            ['/bar',[Tokens::ORDINARY]]
        ];
    }
}
