<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\IR;
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
        $expected = new IR([new NonTerm(Tokens::FIELD,[new NonTerm(Tokens::COLOR, [$term])])]);
        $parser = new Parser();
        $root = $parser->parse($blazon);
        $this->assertEquals($expected, $root);
    }

    /**
     * @test
     * @dataProvider partitions
     * @param string $blazon
     * @param NonTerm $field
     */
    public function checkResultPartition(string $blazon, NonTerm $field): void
    {
        $expected = new IR([$field]);
        $parser = new Parser();
        $root = $parser->parse($blazon);
        self::assertEquals($expected, $root);
    }

    /**
     * @test
     * @dataProvider wrongBlazons
     * @dataProvider longerBlazons
     * @param string $blazon
     * @param array $nodes
     */
    public function checkArray(string $blazon, array $nodes): void
    {
        $parser = new Parser();
        $root = $parser->parse($blazon);
        self::assertEquals($nodes, array_values($root->getChildren()));
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
            ["per bend azure and gules, a bend",[
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
            ["Azure, a or", [
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::TINCTURE, 'azure')
                    ])
                ]),
                new Term(Tokens::COMMA, ','),
                new Term(Tokens::ONE, 'a'),
                new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'or')]),
            ]]
        ];
    }
}
