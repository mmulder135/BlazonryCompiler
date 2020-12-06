<?php

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\Language;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{

    /**
     * @test
     * @dataProvider basicWords
     * @dataProvider simpleSentences
     * @param string $input
     * @param array $expected
     */
    public function checkTokenization(string $input, array $expected): void
    {
        $lan = new Language($input);
        $tokens = $lan->getTokens();
        $this->assertEqualS($expected, $tokens, "Failed on '{$input}'");
    }

    public function basicWords(): array
    {
        return [
            ["azure", [Language::COLOR]],
            ["argent", [Language::COLOR]],
            ["bar", [Language::ORDINARY]],
            ["a", [Language::PREPOSITION]],
            ["s", [Language::STR]],
            [",", [Language::COMMA]],
            ["asdf", [Language::STR]],
            [" ",[]],
        ];
    }

    public function simpleSentences(): array
    {
        return [
            ['azure argent', [Language::COLOR, Language::COLOR]],
            ['bar, as', [Language::ORDINARY, Language::COMMA, Language::STR]]
        ];
    }
}
