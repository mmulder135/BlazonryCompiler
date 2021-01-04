<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\AST\Term;
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
        $ir = $parser->parse('vair');
        $f = $g->generateField($ir->getNodes()[0]);
        file_put_contents('test2.svg', $f->asXML());
    }
}
