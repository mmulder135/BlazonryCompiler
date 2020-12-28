<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Language\Tokens;
use BlazonCompiler\Compiler\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParseFieldTest extends TestCase
{

    /**
     * @test
     * @dataProvider oneColor
     * @param string $blazon
     * @param Term $term
     */
    public function checkResultColor(string $blazon, Term $term): void
    {
        $expected = new NonTerm(
            Tokens::FIELD,
            [new NonTerm(Tokens::COLOR, [$term])]
        );
        $parser = new Parser();
        $ir = $parser->parse($blazon);
        $this->assertEquals($expected, $ir->getNodes()[0]);
    }

    /**
     * @test
     * @dataProvider ordinaries
     * @param string $blazon
     * @param NonTerm $field
     */
    public function checkResultPartition(string $blazon, NonTerm $field): void
    {
        $parser = new Parser();
        $ir = $parser->parse($blazon);
        self::assertEquals([$field], $ir->getNodes());
    }

    public function oneColor(): array
    {
        return [
            ["azure",new Term(Tokens::TINCTURE, "azure")],
            ["purpure",new Term(Tokens::TINCTURE, "purpure")],
            ["or",new Term(Tokens::METAL, "or")],
            ["vair",new Term(Tokens::FUR, "vair")],
        ];
    }

    public function ordinaries(): array
    {
        return [
            ["per bend azure and gules",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'bend')
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'gules')])
                ])
            ]
        ];
    }
}
