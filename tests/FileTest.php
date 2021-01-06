<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Parser\Parser;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /** @test */
    public function createFile(): void
    {
        $g = new CodeGenerator();
        $parser = new Parser();
        $ir = $parser->parse('ermine');
        $f = $g->generateField($ir->getNodes()[0]);
        file_put_contents('test.svg', $f->saveXML());
    }
}
