<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Language\Tokens;
use BlazonCompiler\Compiler\Parser\Parser;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class GenerateFieldTest extends TestCase
{
//    /**
//     * @test
//     * @dataProvider toGenerate
//     */
//    public function generateFiles(string $blazon, string $filename): void
//    {
//        $g = new CodeGenerator();
//        $parser = new Parser();
//        $ir = $parser->parse($blazon);
//        $f = $g->generateWithEdge(new NonTerm(Tokens::SHIELD, $ir->getNodes()));
//        file_put_contents("generated/".$filename, $f->saveXML());
//    }

    public function toGenerate(): array
    {
        return [
//            ["azure","azure.svg"],
//            ["vair","vair.svg"],
//            ["ermine","ermine.svg"],
//            ["per bend vair and or","per_bend_vair_or.svg"],
//            ["per bend or and vair","per_bend_or_vair.svg"],
//            ["per pale vair and or","per_pale_vair_or.svg"],
            ["per bend sinister vair and or","per_bend_sinister_vair_or.svg"],
            ["per bend sinister or and vair","per_bend_sinister_or_vair.svg"]
        ];
    }

    /**
     * @test
     * @dataProvider singleColor
     * @dataProvider partition
     * @dataProvider sinister
     * @dataProvider noField
     * @param string $blazon
     * @param string $fileName
     */
    public function test(string $blazon, string $fileName): void
    {
        $g = new CodeGenerator();
        $parser = new Parser();
        $ir = $parser->parse($blazon);
        $xml = $g->generate(new NonTerm(Tokens::SHIELD, $ir->getNodes()))->saveXML();
        $result = new DOMDocument();
        $result->preserveWhiteSpace = false;
        $result->formatOutput = false;
        $result->loadXML($xml);

        $expected = new DOMDocument();
        $expected->preserveWhiteSpace = false;
        $expected->formatOutput = false;
        $path = dirname(__FILE__, 2)."/images/{$fileName}.svg";
        $expected->load($path);

        self::assertEquals($expected, $result);
    }

    public function singleColor(): array
    {
        return [
            ['azure','azure'],
            ['vair','vair'],
            ['Ermine','ermine'],
        ];
    }

    public function partition(): array
    {
        return [
            ['per bend argent and or','per_bend_argent_or'],
            ['per bend ermine and vair','per_bend_ermine_vair'],
            ['per bend ermine and or', 'per_bend_ermine_or'],
            ['per bend or and ermine', 'per_bend_or_ermine'],
            ['per pale argent and or', 'per_pale_argent_or'],
            ['per pale ermine and vair', 'per_pale_ermine_vair'],
        ];
    }

    public function sinister(): array
    {
        return [
            ['per bend sinister argent and or','per_bend_sinister_argent_or'],
            ['per pale sinister argent and or', 'per_pale_argent_or'],
            ['per bend sinister ermine and vair','per_bend_sinister_ermine_vair'],
        ];
    }

    public function noField(): array
    {
        return [
            ['per bend argent', 'error_shield'],
            ['bend','error_shield'],
        ];
    }
}
