<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\Node;
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
     * @dataProvider partitions
     * @param string $blazon
     * @param NonTerm $field
     */
    public function checkResultPartition(string $blazon, NonTerm $field): void
    {
        $parser = new Parser();
        $ir = $parser->parse($blazon);
        self::assertEquals([$field], $ir->getNodes());
    }

    /**
     * @test
     * @dataProvider wrongBlazons
     * @dataProvider longerBlazons
     * @param string $blazon
     * @param Node[] $expected
     */
    public function checkArray(string $blazon, array $expected): void
    {
        $parser = new Parser();
        $ir = $parser->parse($blazon);
        self::assertEquals($expected, array_values($ir->getNodes()));
    }

    /**
     * @return array<array<string,Term>>
     */
    public function oneColor(): array
    {
        return [
            ["azure",new Term(Tokens::TINCTURE, "azure")],
            ["purpure",new Term(Tokens::TINCTURE, "purpure")],
            ["or",new Term(Tokens::METAL, "or")],
            ["vair",new Term(Tokens::FUR, "vair")],
        ];
    }

    /**
     * @return array<array<string,NonTerm>>
     */
    public function partitions(): array
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
            ],
            ["per pale argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'pale')
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
            ["Per pale, argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'pale')
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
            ["Party per pale argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'pale')
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
            ["per bend sinister argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'bend'),
                    ]),
                    new Term(Tokens::SINISTER, 'sinister'),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
            ["per bend engrailed argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'bend'),
                    ]),
                    new Term(Tokens::PARTITION_LINE, 'engrailed'),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
            ["per bend sinister engrailed argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'bend'),
                    ]),
                    new Term(Tokens::SINISTER, 'sinister'),
                    new Term(Tokens::PARTITION_LINE, 'engrailed'),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
            ["per bend engrailed sinister  argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'bend'),
                    ]),
                    new Term(Tokens::PARTITION_LINE, 'engrailed'),
                    new Term(Tokens::SINISTER, 'sinister'),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
            ["Quarterly argent and sable",
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::DIVISION, 'quarterly'),
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'sable')])
                ])
            ],
        ];
    }


    /**
     * @return array<array<string|Node[]>>
     */
    public function wrongBlazons(): array
    {
        return [
            ["per bend sinister argent",[
                new NonTerm(Tokens::PARTITION, [
                    new Term(Tokens::PER, 'per'),
                    new Term(Tokens::ORDINARY, 'bend'),
                ]),
                new Term(Tokens::SINISTER, 'sinister'),
                new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
            ]],
            ["bend",[
                new Term(Tokens::ORDINARY, 'bend')
            ]]
        ];
    }

    public function longerBlazons(): array
    {
        return [
            ["per bend azure and gules, a bend purpure",[
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'bend')
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'gules')])
                ]),
                new Term(Tokens::COMMA, ','),
                new Term(Tokens::ONE, 'a'),
                new Term(Tokens::ORDINARY, 'bend'),
                new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'purpure')]),
            ]],
            ["per bend azure and gules, purpure",[
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'bend')
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'gules')])
                ]),
                new Term(Tokens::COMMA, ','),
                new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'purpure')]),
            ]],
            ["Azure, a bend or", [
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::TINCTURE, 'azure')
                    ])
                ]),
                new Term(Tokens::COMMA, ','),
                new Term(Tokens::ONE, 'a'),
                new Term(Tokens::ORDINARY, 'bend'),
                new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'or')]),
            ]]
        ];
    }
}
