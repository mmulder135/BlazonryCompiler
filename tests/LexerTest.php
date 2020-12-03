<?php

namespace compiler;

use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public function recognizeTincture()
    {
        $input = array('root -> Foo::bar');
        $result = Lexer::run($input);
        var_dump($result);
        self::assertTrue(true);
    }
}
