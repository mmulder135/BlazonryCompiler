<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Language\Tokens;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /** @test */
    public function createFile(): void
    {
        $g = new CodeGenerator();
        $f = $g->generateField(new NonTerm(Tokens::FIELD,[new NonTerm(Tokens::COLOR, [new Term(Tokens::FUR, 'azure')])]));
        file_put_contents('test.svg', $f->asXML());
    }
}
