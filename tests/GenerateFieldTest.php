<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Parser\Parser;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class GenerateFieldTest extends TestCase
{
    /**
     * @test
     * @dataProvider singleColor
     * @dataProvider partition
     * @dataProvider sinister
     * @dataProvider noField
     * @param string $blazon
     * @param string $fileName
     * @param bool $errors
     */
    public function test(string $blazon, string $fileName, bool $errors = false): void
    {
        $g = new CodeGenerator();
        $parser = new Parser();
        $root = $parser->parse($blazon);
        $xml = $g->generate($root)->saveXML();
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
        self::assertEquals($errors,count($root->getMessages()) > 0);
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
            ['per pale sinister argent and or', 'per_pale_argent_or',true],
            ['per bend sinister ermine and vair','per_bend_sinister_ermine_vair'],
        ];
    }

    public function noField(): array
    {
        return [
            ['per bend argent', 'error_shield',true],
            ['bend','error_shield',true],
        ];
    }
}
