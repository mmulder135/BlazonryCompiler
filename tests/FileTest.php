<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Language\Terminals;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /** @test */
    public function createFile(): void
    {
        $g = new CodeGenerator();
        $f = $g->generate(new NonTerm('color',[new Term(Terminals::METAL,'purpure')]));
        file_put_contents('test.svg', $f->asXML());
    }
}