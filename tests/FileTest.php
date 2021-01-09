<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Language\Tokens;
use BlazonCompiler\Compiler\Parser\Parser;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /** @test */
    public function createFile(): void
    {
        $g = new CodeGenerator();
        $parser = new Parser();
        $ir = $parser->parse('per bend vair and or');
        $f = $g->generate(new NonTerm(Tokens::SHIELD,$ir->getNodes()));
        file_put_contents('test.svg', $f->saveXML());
    }
}
