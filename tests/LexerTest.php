<?php

namespace compiler;

use BlazonCompiler\Compiler\Language;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{

    protected function checkTokenization(string $input, array $expected): void
    {
        $lan = new Language($input);
        $tokens = $lan->getTokens();
        $this->assertEqualS($expected, $tokens, "Failed on '{$input}'");
    }

    public function testBasicWords(): void
    {
        $this->checkTokenization("azure", [ Language::COLOR]);
        $this->checkTokenization("argent", [Language::COLOR]);
        $this->checkTokenization("bar", [ Language::ORDINARY]);
        $this->checkTokenization("a", [ Language::PREPOSITION]);
        $this->checkTokenization("s", [ Language::STR]);
        $this->checkTokenization(",", [ Language::COMMA]);
        $this->checkTokenization("asdf", [Language::STR]);
    }

    public function testSimpleSentence(): void
    {
        $this->checkTokenization("azure argent", [Language::COLOR,Language::COLOR]);
        $this->checkTokenization("bar, as", [Language::ORDINARY,Language::COMMA,Language::STR]);
    }
}
